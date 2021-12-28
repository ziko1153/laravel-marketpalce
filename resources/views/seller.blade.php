<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Stripe Connect</title>

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
                            <img style="width:100px" class="card-img-top" src="https://picsum.photos/200" alt="Card image cap">
                            <div class="card-body">
                              <h5 class="card-title">Hi, {{$seller->name}}</h5>
                              @if (!$seller->completed_stripe_onboarding)
                              <span class="badge badge-danger">Not Connected</span>
                              <br/>
                              <p class=""> Please connect your Stripe account </p>
                          @else
                          <span class="badge badge-success">Connected</span>
                              <h1 href="#" class=""> ${{ $balance }} </h1>

                          @endif
                            </div>

                            <div class="d-flex justify-content-center">
                                <a
                                    type="button"
                                    href="{{ route('redirect.stripe', ['id' => $seller->id]) }}"
                                    class="">
                                    <i class="fa fa-external-link" aria-hidden="true"></i> &nbsp;
                                    @if ($seller->completed_stripe_onboarding)
                                        View Stripe Account
                                    @else
                                        Connect Stripe Account
                                    @endif
                                </a>
                            </div>
                          </div>
                    </div>
                  </div>
        
            </div>
        </div>
        
    </body>

    
</html>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
