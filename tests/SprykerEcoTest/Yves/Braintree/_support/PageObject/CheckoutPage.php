<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Yves\Braintree\PageObject;

class CheckoutPage
{
    public const URL = '/checkout';
    public const URL_CUSTOMER = '/checkout/customer';
    public const URL_ADDRESS = '/checkout/address';
    public const URL_SHIPMENT = '/checkout/shipment';
    public const URL_PAYMENT = '/checkout/payment';
    public const URL_SUMMARY = '/checkout/summary';
    public const URL_SUCCESS = '/checkout/success';

    public const BUTTON_CHECKOUT = 'Checkout';
    public const BUTTON_GO_TO_PAYMENT = 'Go to Payment';

    public const SHIPMENT_SELECTION = 'shipmentForm_idShipmentMethod_0';
}
