<?php
namespace App\Resolvers;

use App\Models\PaymentPlatform;

class PaymentPlatFormResolver 
{
    protected $paymentPlatForms;

    public function __construct()
    {
       $this->paymentPlatForms = PaymentPlatform::all(); 
    }

    public function resolveService($paymentPlatformId)
    {
        $name = strtolower($this->paymentPlatForms->firstWhere('id',$paymentPlatformId)->name);
        $service = config("services.{$name}.class");
        if($service){
            return resolve($service);
        }
        throw new \Exception('The Selected platform is not supported !');
    }
}
