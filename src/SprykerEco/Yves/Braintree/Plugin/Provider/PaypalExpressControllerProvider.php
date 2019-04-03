<?php

namespace SprykerEco\Yves\Braintree\Plugin\Provider;

use Silex\Application;
use SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider;

class PaypalExpressControllerProvider extends AbstractYvesControllerProvider
{
    public const ROUTE_PAYPAL_EXPRESS_SUCCESS_RESPONSE = 'paypal-express-success';

    /**
     * @param \Silex\Application $app
     *
     * @return void
     */
    protected function defineControllers(Application $app)
    {
        $this->addPaypalExpressSuccessResponseRoute();
    }

    /**
     * @uses \SprykerEco\Yves\Braintree\Controller\PaypalExpressController::successAction()
     *
     * @return $this
     */
    protected function addPaypalExpressSuccessResponseRoute()
    {
        $this->createController(
            '/paypal-express/payment/success',
            static::ROUTE_PAYPAL_EXPRESS_SUCCESS_RESPONSE,
            'Braintree',
            'PaypalExpress',
            '/paypal-express/payment/success'
        );

        return $this;
    }
}