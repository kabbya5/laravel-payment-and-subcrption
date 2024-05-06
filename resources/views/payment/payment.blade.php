@extends('layouts.app')
@section('link')
<style>
    #card-element {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    }

    #card-errors {
    color: red;
    font-size: 14px;
    margin-top: 10px;
    }
</style>
@endsection
@section('content')
    <div class="container mx-auto">
        <div class="mt-4 shadow-md bg-gray-100 p-10 w-4/6 mx-auto">
            <h2 class="text-center text-2xl"> Make a payment </h2>
            <div class="mt-4">
                <form action="{{route('payment.store')}}" method="post" id="paymentForm">
                    @csrf
                    <div class="flex justify-between">
                        <div class="form-group mb-4 flex flex-col w-4/6">
                            <label for=""> How much want to pay ? </label>
                            <input type="number" step="0.01" name="value" class="mt-2 p-2" min="5" value="{{mt_rand(500,1000) / 100}}">
                        </div>
                        <div class="form-group mb-4 flex flex-col w-2/6">
                            <label for=""> Currenct  </label>
                            <select name="currency" class="mt-2 p-2">
                                @foreach ($currencies as  $currency)
                                    <option value="{{$currency->iso}}"> {{strtoupper($currency->iso)}} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="form-group mb-4 flex flex-col">
                            <label for="">  Select the payment platform  </label>
                            <div class="flex justify-between items-center">
                                @foreach ($paymentPlatForms as $paymentPlateForm)
                                <div class="flex pr-4">
                                    <input type="radio" id="payment-{{$paymentPlateForm->id}}" name="payment_plateform" value="{{$paymentPlateForm->id}}" class="mr-4 black">
                                    <label for="payment-{{$paymentPlateForm->id}}">
                                        <img src="{{$paymentPlateForm->image}}" alt="w-32" class="w-32">
                                    </label>
                                </div>
                                @endforeach 
                            </div> 
                        </div>
                    </div>

                    <div class="my-3 hidden" id="stripe-form">
                        <div id="cardEelement">
                            <!-- A Stripe Element will be inserted here. -->
                          </div>
                        
                          <!-- Used to display form errors. -->
                        <div id="cardErrors" role="alert"></div>

                        <input type="hidden" name="payment_method" id="paymentMethod">
                    </div>

                
                    <div class="text-right mt-5">
                        <button id="payButton" type="submit" class="text-white bg-red-500 px-6 py-2">
                            Pay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://js.stripe.com/v3/"></script>

<script>
    
    $(document).on('click', '#payment-2', function() {
        $('#stripe-form').removeClass('hidden');
    });

    window.addEventListener('DOMContentLoaded', () => { 
        var stripe = Stripe("{{config('services.stripe.key')}}");
        var elements = stripe.elements();

        var cardElement = elements.create('card');

        cardElement.mount('#cardEelement');

        const form = document.getElementById('paymentForm');
        const payButton = document.getElementById('payButton');

        form.addEventListener('submit', async(e) =>{
            if(form.elements.payment_plateform.value ==="{{$paymentPlateForm->id}}"){
                e.preventDefault();
                const {paymentMethod, error} = await stripe.createPaymentMethod(
                    'card', cardElement, {
                        billing_details:{
                            'name': '{{auth()->user()->name}}',
                            'email' : '{{auth()->user()->email}}',
                        }
                    }
                );

                if(error){
                    const displayError = document.getElementById('cardErrors');
                    displayError.textContent = error.message;
                }else{
                    const tokenInput = document.getElementById('paymentMethod');
                    tokenInput.value = paymentMethod.id;
                    form.submit();
                }
            }
        })
    })

    

    

</script>
@endsection