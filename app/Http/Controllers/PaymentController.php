<?php 
namespace App\Http\Controllers;

use App\Resolvers\PaymentPlatFormResolver;
use Illuminate\Http\Request;
class PaymentController extends Controller{

    protected $paymentPlatFormResolver;

    public function  __construct(PaymentPlatFormResolver $paymentPlatFormResolver)
    {
        $this->middleware('auth');
        $this->paymentPlatFormResolver = $paymentPlatFormResolver;
    }

    public function store(Request $request){
        $rules = [
            'value' => ['required','numeric', 'min:3'],
            'currency' => ['required','exists:currencies,iso'],
            'payment_plateform' => ['required','exists:payment_platforms,id']
        ];

       


        $request->validate($rules);
        $paymentPalatForm = $this->paymentPlatFormResolver->resolveService($request->payment_plateform);
        
        session()->put('paymentPlatFormId',$request->payment_plateform);

        return $paymentPalatForm->handelPayment($request);
        
    }

    public function approval(){
        if(session()->has('paymentPlatFormId')){
            $paymentPalatForm = $this->paymentPlatFormResolver->resolveService(session()->get('paymentPlatFormId'));
            return $paymentPalatForm->handelApproval();
        }
        return redirect()->route('home')->withErrors("Invalid payment platform !");
    }

    public function cancelled(){
        return redirect()->route('home')->withErrors("The payment has been cancelled");
    }

    public function config(){
        return config('services.stripe.key');
    }
}