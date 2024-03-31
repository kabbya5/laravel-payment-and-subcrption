<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaypalService;
class PaymentController extends Controller{

    /**
     * Store the payment data
    */

    public function store(Request $request){
        
        $rules = [
            'value' => ['required','numeric', 'min:3'],
            'currency' => ['required','exists:currencies,iso'],
            'payment_plateform' => ['required','exists:payment_platforms,id']
        ];

        $request->validate($rules);
        $paymentPalatForm = resolve(PaypalService::class);
        
        return $paymentPalatForm->handelPayment($request);
        
    }

    public function approval(){
        $paymentPalatForm = resolve(PaypalService::class);
        return $paymentPalatForm->handelApproval();
    }

    public function cancle(){
        //
    }
}