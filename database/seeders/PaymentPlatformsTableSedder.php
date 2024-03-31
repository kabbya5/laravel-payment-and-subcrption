<?php

namespace Database\Seeders;

use App\Models\PaymentPlatform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentPlatformsTableSedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentPlatform::create([
            'name' => 'Paypal',
            'image' => 'img/payament-platforms/payple.jpg',
        ]);

        PaymentPlatform::create([
            'name' => 'stripe',
            'image' => 'img/payament-platforms/strip.jpg',
        ]);
    }
}
