<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Accept a payment</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="{{ URL::asset('css/checkout.css') }}" />
        <script src="https://js.stripe.com/v3/"></script>
        {{-- <script src="{{ URL::asset('js/checkout.js') }}" defer></script> --}}

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
        <script src="https://js.stripe.com/v3/"></script>
    </head>
    <body>
    </head>

    <body>
        <div class="container">
            @if ($errors->any())
            <h4 class="text-danger"> {{ $errors->first() }} </h4>
        @endif
            <div class="row mt-4">
                <div class="card">
                    <div class="card-body">

                            <div class="card">
                                  <!-- Display a payment form -->
                                    <form id="payment-form">
                                            <div id="payment-element">
                                            <!--Stripe.js injects the Payment Element-->
                                            </div>
                                            <button id="submit">
                                            <div class="spinner hidden" id="spinner"></div>
                                            <span id="button-text">Pay now</span>
                                            </button>
                                            <div id="payment-message" class="hidden"></div>
                                    </form>
                                
                            </div>

                           
                          </div>
                    </div>
                  </div>
        
            </div>
        </div>
        
    </body>

    
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<script>
    // This is a public sample test API key.
// To avoid exposing it, don't submit any personally identifiable information through requests with this API key.
// Sign in to see your own test API key embedded in code samples.

const stripe = Stripe("pk_test_51KBZlzJgFvcrVivucaFfLGh79gzzEXKNpa69LsNhwfe14hI7iJDS2Yfr82FA64d0dUmtvdZsbCsRsLH2vpKFPvmU00febK6Qbf",{
  stripeAccount: '{{$stripe_account}}'
});

// The items the customer wants to buy
const items = [{ id: "xl-tshirt" }];

let elements;

initialize();
checkStatus();

document
    .querySelector("#payment-form")
    .addEventListener("submit", handleSubmit);

// Fetches a payment intent and captures the client secret
async function initialize() {
    var data = {
        
    };
    var headers = {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    let clientSecret;
    request = $.ajax({
        url: "/token",
        type: "post",
        headers: headers,
        data: data
    });
request.done(function (data){
    console.log("It works!",data);
    clientSecret = data.clientSecret;
     elements = stripe.elements({ clientSecret });

    const paymentElement = elements.create("payment");
    paymentElement.mount("#payment-element");
});

 
}

async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);

    const { error } = await stripe.confirmPayment({
        elements,
        
    });


    if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message);
    } else {
        showMessage("An unexpected error occured.");
    }

    setLoading(false);
}

// Fetches the payment intent status after payment submission
async function checkStatus() {
    const clientSecret = new URLSearchParams(window.location.search).get(
        "payment_intent_client_secret"
    );

    if (!clientSecret) {
        return;
    }

    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

    switch (paymentIntent.status) {
        case "succeeded":
            showMessage("Payment succeeded!");
            break;
        case "processing":
            showMessage("Your payment is processing.");
            break;
        case "requires_payment_method":
            showMessage("Your payment was not successful, please try again.");
            break;
        default:
            showMessage("Something went wrong.");
            break;
    }
}

// ------- UI helpers -------

function showMessage(messageText) {
    const messageContainer = document.querySelector("#payment-message");

    messageContainer.classList.remove("hidden");
    messageContainer.textContent = messageText;

    setTimeout(function () {
        messageContainer.classList.add("hidden");
        messageText.textContent = "";
    }, 4000);
}

// Show a spinner on payment submission
function setLoading(isLoading) {
    if (isLoading) {
        // Disable the button and show a spinner
        document.querySelector("#submit").disabled = true;
        document.querySelector("#spinner").classList.remove("hidden");
        document.querySelector("#button-text").classList.add("hidden");
    } else {
        document.querySelector("#submit").disabled = false;
        document.querySelector("#spinner").classList.add("hidden");
        document.querySelector("#button-text").classList.remove("hidden");
    }
}
</script>
