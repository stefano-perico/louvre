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
            $this->setErrors($exception);
            /**
            $body = $exception->getJsonBody();
            $err = $body['error'];

            print ('Type is:' . $err['type'] . "\n");
            print ('Code is:' . $err['code'] . "\n");
            //print ('Param is:' . $err['param'] . "\n");
            print ('Message is:' . $err['message'] . "\n");

            $this->errors[] = $err['message'];
             *  */
        }
        catch (\Stripe\Error\RateLimit $exception){
            $this->setErrors($exception);
        }
        catch (\Stripe\Error\InvalidRequest $exception){
            $this->setErrors($exception);
        }
        catch (\Stripe\Error\Authentication $exception){
            $this->setErrors($exception);
        }
        catch (\Stripe\Error\ApiConnection $exception){
            $this->setErrors($exception);
        }
        catch (\Stripe\Error\Base $exception){
            $this->setErrors($exception);
        }
        catch (\Exception $exception){
            $this->setErrors($exception);
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

    public function setErrors($exception)
    {
        $body = $exception->getJsonBody();
        $err = $body['error'];
        $this->errors[] = $err['message'];
    }

}