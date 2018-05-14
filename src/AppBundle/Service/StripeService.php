<?php


namespace AppBundle\Service;

use Stripe\Stripe;

class StripeService
{
    private $errors = [];

    public function __construct($stripeSecretKey)
    {
        Stripe::setApiKey($stripeSecretKey);
    }

    public function charge($token, $prix)
    {
        try
        {
            $charge = \Stripe\Charge::create([
                "amount" => $prix,
                "currency" => "eur",
                "description" => "Example charge",
                "source" => $token,
            ]);
            return $charge;
        }
        catch (\Stripe\Error\Card $exception) {
            $body = $exception->getJsonBody();
            $err = $body['error'];
            /**
            print ('Type is:' . $err['type'] . "\n");
            print ('Code is:' . $err['code'] . "\n");
            //print ('Param is:' . $err['param'] . "\n");
            print ('Message is:' . $err['message'] . "\n");
             */
            $this->errors[] = $err['message'];
        }
        catch (\Stripe\Error\RateLimit $exception){

        }
        catch (\Stripe\Error\InvalidRequest $exception){

        }
        catch (\Stripe\Error\Authentication $exception){

        }
        catch (\Stripe\Error\ApiConnection $exception){

        }
        catch (\Stripe\Error\Base $exception){

        }
        catch (\Exception $exception){

        }
    return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}