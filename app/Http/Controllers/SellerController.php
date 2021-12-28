<?php

namespace App\Http\Controllers;

use App\Models\User;
use Stripe\StripeClient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\DatabaseManager;

class SellerController extends Controller
{
    protected $stripeClient;
    protected $databaseManager;
    public function __construct(StripeClient $stripeClient, DatabaseManager $databaseManager)
    {
        $this->stripeClient = $stripeClient;
        $this->databaseManager = $databaseManager;
    }

    public function show($id)
    {

        $seller = User::findOrFail($id);
        $balance =  $seller->completed_stripe_onboarding ?  $this->stripeClient
            ->balance->retrieve(null, ['stripe_account' => $seller->stripe_connect_id])
            ->available[0]
            ->amount : 0;


        dd($this->stripeClient->accounts->allCapabilities($seller->stripe_connect_id));


        return view('seller', ['seller' => $seller, 'balance' => $balance]);
    }

    public function redirectToStripe($id)
    {


        $seller = User::find($id);

        if (is_null($seller)) {
            abort(404);
        }

        // Complete the onboarding process
        if (!$seller->completed_stripe_onboarding) {
            $token = Str::random();

            $this->databaseManager->table('stripe_state_tokens')->insert([
                'created_at' => now(),
                'updated_at' => now(),
                'seller_id'  => $seller->id,
                'token'      => $token
            ]);

            try {

                // Let's check if they have a stripe connect id
                if (is_null($seller->stripe_connect_id)) {

                    // Create account
                    $account = $this->stripeClient->accounts->create([
                        'country' => 'DE',
                        'type'    => 'express',
                        'email'   => $seller->email,
                    ]);

                    $seller->update(['stripe_connect_id' => $account->id]);
                    $seller->fresh();
                }

                $onboardLink = $this->stripeClient->accountLinks->create([
                    'account'     => $seller->stripe_connect_id,
                    'refresh_url' => route('redirect.stripe', ['id' => $seller->id]),
                    'return_url'  => route('save.stripe', ['token' => $token]),
                    'type'        => 'account_onboarding'
                ]);

                // dd($onboardLink);

                return redirect($onboardLink->url);
            } catch (\Exception $exception) {
                return back()->withErrors(['message' => $exception->getMessage()]);
            }
        }

        try {

            $loginLink = $this->stripeClient->accounts->createLoginLink($seller->stripe_connect_id);
            return redirect($loginLink->url);
        } catch (\Exception $exception) {
            return back()->withErrors(['message' => $exception->getMessage()]);
        }
    }


    public function saveStripeAccount($token)
    {
        $stripeToken = $this->databaseManager->table('stripe_state_tokens')
            ->where('token', '=', $token)
            ->first();

        if (is_null($stripeToken)) {
            abort(404);
        }

        $seller = User::find($stripeToken->seller_id);

        $seller->update([
            'completed_stripe_onboarding' => true
        ]);

        return redirect()->route('seller.profile', ['id' => $seller->id]);
    }

    public function deleteAccount()
    {
        // dd($this->stripeClient->accounts->delete(
        //     'acct_1KBcOYR9B63GUpYy',
        //     []
        // ));
    }

    public function checkout($userId)
    {
        $seller = User::find($userId);

        return view('checkout', ['stripe_account' => $seller->stripe_connect_id]);
    }

    public function createToken(Request $request)
    {

        $seller = User::find(2);
        $paymentIntent = $this->stripeClient->paymentIntents->create([
            'amount' => 100,
            'currency' => 'eur',
            'payment_method_types' => ['card', 'sepa_debit'],
            'receipt_email' => 'ziko.nwu@gmail.com',
            'customer' => ''

        ], ['stripe_account' => $seller->stripe_connect_id]);

        // dd($paymentIntent);

        return response()->json(['clientSecret' => $paymentIntent->client_secret]);
    }
}
