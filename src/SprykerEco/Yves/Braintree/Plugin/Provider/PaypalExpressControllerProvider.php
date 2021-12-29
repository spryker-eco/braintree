<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Plugin\Provider;

use Silex\Application;
use SprykerShop\Yves\ShopApplication\Plugin\Provider\AbstractYvesControllerProvider;

class PaypalExpressControllerProvider extends AbstractYvesControllerProvider
{
    /**
     * @var string
     */
    public const ROUTE_PAYPAL_EXPRESS_SUCCESS_RESPONSE = 'paypal-express-success';

    /**
     * @var string
     */
    public const ROUTE_PAYPAL_EXPRESS_SHIPMENT_ADD = 'paypal-express-shipment-add';

    /**
     * @param \Silex\Application $app
     *
     * @return $this
     */
    protected function defineControllers(Application $app)
    {
        $this->addPaypalExpressSuccessResponseRoute();
        $this->addPaypalExpressAddShipmentRoute();

        return $this;
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
            'success',
        );

        return $this;
    }

    /**
     * @uses \SprykerEco\Yves\Braintree\Controller\PaypalExpressController::successAction()
     *
     * @return $this
     */
    protected function addPaypalExpressAddShipmentRoute()
    {
        $this->createPostController(
            '/paypal-express/shipment/add',
            static::ROUTE_PAYPAL_EXPRESS_SHIPMENT_ADD,
            'Braintree',
            'PaypalExpress',
            'addShipment',
        );

        return $this;
    }
}
