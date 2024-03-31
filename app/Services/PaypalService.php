<?php 

namespace App\Services;

use App\Traits\ConsumesExternalServies;
use Illuminate\Http\Request;

class PaypalService{
    use ConsumesExternalServies;
    protected $baseUri;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->baseUri = config('services.paypal.base_uri');
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
    }

    public function resolveAuthorization(&$queryParams,&$formParam,&$headers)
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response)
    {
        return json_decode($response);
    }

    public function resolveAccessToken()
    {
        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");
        return "Basic {$credentials}";
    }

    public function handelPayment(Request $request){
        $order = $this->createOrder($request->value, $request->currency);
        $orderLinks = collect($order->links);

        $approve = $orderLinks->where('rel','approve')->first();
        return redirect($approve->href);
    }

    public function createOrder($value,$currency){
        return $this->makeRequest(
            'POST',
            'v2/checkout/orders',
            [],
            [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    0 =>[
                        'amount' => [
                            'currency_code' => strtoupper($currency),
                            'value' => $value,
                        ]
                    ]
                ],

                'application_context' => [
                    'brand_name' => config('app.name'),
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                    'return_url' => route('payment.approval'),
                    'cancel_url' => route('payment.cancle'),
                ]
            ],
            [],
            $isJsonRequest = true,
        );          
    }

    public function capturePayment($approvalId)
    { 
        return $this->makeRequest(
            'POST',
            "/v2/checkout/orders/{$approvalId}/capture",
            [],
            [],
            [
                'Content-Type' => 'application/json',
            ],
        );
    }


}