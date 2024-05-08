@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <p class="text-red-500"> {{ isset($status) ? ' ' . $status . ' ' .  $clientSecret : '' }} </p>
                    <p> You need to follow some steps with your bank to complete this payment. Let's go! </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://js.stripe.com/v3/"></script>
<script>    

    const stripe = Stripe('{{ config("services.stripe.key") }}');
    stripe.confirmCardPayment('{{$clientSecret}}')
    .then(function(result) {
        if (result.error) {
            window.location.replace("{{ route('payment.cancel') }}");
        } else {
            window.location.replace("{{ route('payment.approval') }}");
        }
    });

</script>
   

@endsection