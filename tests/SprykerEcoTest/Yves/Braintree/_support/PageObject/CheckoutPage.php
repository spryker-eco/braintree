<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Yves\Braintree\PageObject;

class CheckoutPage
{
    /**
     * @var string
     */
    public const URL = '/checkout';

    /**
     * @var string
     */
    public const URL_CUSTOMER = '/checkout/customer';

    /**
     * @var string
     */
    public const URL_ADDRESS = '/checkout/address';

    /**
     * @var string
     */
    public const URL_SHIPMENT = '/checkout/shipment';

    /**
     * @var string
     */
    public const URL_PAYMENT = '/checkout/payment';

    /**
     * @var string
     */
    public const URL_SUMMARY = '/checkout/summary';

    /**
     * @var string
     */
    public const URL_SUCCESS = '/checkout/success';

    /**
     * @var string
     */
    public const BUTTON_CHECKOUT = 'Checkout';

    /**
     * @var string
     */
    public const BUTTON_GO_TO_PAYMENT = 'Go to Payment';

    /**
     * @var string
     */
    public const SHIPMENT_SELECTION = 'shipmentForm_idShipmentMethod_0';
}
