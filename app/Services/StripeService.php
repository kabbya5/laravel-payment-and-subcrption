<?php
namespace App\Services;

use App\Traits\ConsumesExternalServies;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

use function Laravel\Prompts\error;

Class StripeService
{
    use ConsumesExternalServies;

    protected $baseUri;
    protected $key;
    protected $secret; 

    public function __construct(){
        $this->baseUri = config('services.stripe.base_uri');
        $this->key = config('services.stripe.key');
        $this->secret = config('services.stripe.secret');
    }

    public function resolveAuthorization(&$queryParams,&$formParams,&$headers){
        $headers['Authorization'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response){
        return json_decode($response);
    }

    public function resolveAccessToken(){
        return "Bearer $this->secret";
    }

    public function handelPayment(Request $request){
        $request->validate([
            'payment_method' => 'required',
        ]);

        $intent = $this->createIntent($request->value,$request->currency,$request->payment_method);

        session()->put('paymenIntentId',$intent->id);

        return redirect()->route('payment.approval');
    }

    public function createIntent($value,$currency,$paymentMethod){
        return $this->makeRequest(
            'POST',
            '/v1/payment_intents',
            [],
            [
                'amount' => round($value * $this->resolveFactor($currency)),
                'currency' => strtolower($currency),
                'payment_method' => $paymentMethod, // Corrected parameter name
            ]
        );
    }

    public function confirmPayment($paymentIntentId){
        return $this->makeRequest(
            'POST',
            "/v1/payment_intents/{$paymentIntentId}/confirm"
        );
        
    }

    public function handelApproval(){
        if(session()->has('paymenIntentId')){
            $paymentIntentId = session()->get('paymenIntentId');
            try{
                $confirmation = $this->confirmPayment($paymentIntentId);
            }catch(RequestException $e){
                $body = $e->getResponse()->getBody()->getContents();
                $error = $this->decodeResponse($body)->error;
                
              
                $clientSecret = $error->payment_intent->client_secret;
                $status = $error->code;
    
    
                if($clientSecret && $status){
                    return view('stripe.3d-secure')->with([
                        'clientSecret' => $clientSecret,
                        'status' => $status,
                    ]);
                }
            }

            if($confirmation->status ==='requires_action'){
                $clientSecret = $confirmation->client_secret;
                return view('stripe.3d-secure')->with([
                    'status' => '3d-secure',
                    'clientSecret' => $clientSecret,
                ]);
            }

            if($confirmation->status === 'succeeded'){
                $name = auth()->user()->name;
                $currency = strtoupper($confirmation->currency);
                $amount = $confirmation->amount / $this->resolveFactor($currency);

                return redirect()
                        ->route('home')
                        ->withSuccess("Thanks, {$name}. We received your {$amount} {$currency} Payment");
            } 
                
            return redirect()
                    ->route('home')
                    ->withErrors("We encountered an unexpected response from Stripe. Please try again later.");
            
        }
    
        // No payment intent ID found in session
        return redirect()->route('home')->withErrors('We are unable to confirm your Payment. Please try again.');
    }
    public function resolveFactor($currency){
        $zeroDecimalCurrencies = ['JPY'];

        if(in_array(strtoupper($currency),$zeroDecimalCurrencies)){
            return 1;
        }
         
        return 100;
    }
}
