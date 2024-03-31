@extends('layouts.app')
@section('content')
    <div class="container mx-auto">
        <div class="mt-4 shadow-md bg-gray-100 p-10 w-4/6 mx-auto">
            <h2 class="text-center text-2xl"> Make a payment </h2>
            <div class="mt-4">
                <form action="{{route('payment.store')}}" method="post">
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
                    
                    <div class="text-right">
                        <button type="submit" class="text-white bg-red-500 px-6 py-2">
                            Pay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection