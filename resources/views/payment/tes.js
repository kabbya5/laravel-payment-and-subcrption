const {paymentMethod, error} = await stripe.createPaymentMethod(
    'card', cardElement, {
        billing_details:{
            'name': '{{auth()->user()->name}}',
            'email' : '{{auth()->user()->email}}',
        }
    }
);

if(error){
    const displayError = document.getElementById('cardError');
    displayError.textContent = error.message;
}else{
    const tokenInput = document.getElementById('paymentMethod');
    tokenInput.value = paymentMethod.id;
    form.submit();
}