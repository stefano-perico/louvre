<?php


namespace AppBundle\Service;

use Stripe\Stripe;

class StripeService
{
    public function __construct($stripeSecretKey)
    {
        Stripe::setApiKey($stripeSecretKey);
    }

    public function charge($token, $prix)
    {
        $charge = \Stripe\Charge::create([
            "amount" => $prix,
            "currency" => "eur",
            "description" => "Example charge",
            "source" => $token,
        ]);
        return $charge;
    }

}