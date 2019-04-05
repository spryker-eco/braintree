<?php

namespace SprykerEco\Yves\Braintree\Model\Mapper\PaypalResponse;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class PaypalResponseMapper implements PaypalResponseMapperInterface
{
    protected const KEY_NONCE = 'nonce';
    protected const KEY_DETAILS = 'details';
    protected const KEY_EMAIL = 'email';
    protected const KEY_FIRST_NAME = 'firstName';
    protected const KEY_LAST_NAME = 'lastName';
    protected const KEY_PAYER_ID = 'payerId';
    protected const KEY_SHIPPING_ADDRESS = 'shippingAddress';
    protected const KEY_RECIPIENT_NAME = 'recipientName';
    protected const KEY_LINE1 = 'line1';
    protected const KEY_CITY = 'city';
    protected const KEY_STATE = 'state';
    protected const KEY_POSTAL_CODE = 'postalCode';
    protected const KEY_COUNTRY_CODE = 'countryCode';

    /**
     * @param array $payload
     *
     * @return PaypalExpressSuccessResponseTransfer
     */
    public function mapSuccessResponseToPaypalExpressSuccessResponseTransfer(array $payload): PaypalExpressSuccessResponseTransfer
    {
        $transfer = new PaypalExpressSuccessResponseTransfer();

        $transfer->setNonce($payload[static::KEY_NONCE] ?? null);
        $transfer->setEmail($payload[static::KEY_DETAILS][static::KEY_EMAIL] ?? null);
        $transfer->setFirstName($payload[static::KEY_DETAILS][static::KEY_FIRST_NAME] ?? null);
        $transfer->setLastName($payload[static::KEY_DETAILS][static::KEY_LAST_NAME] ?? null);
        $transfer->setPayerId($payload[static::KEY_DETAILS][static::KEY_PAYER_ID] ?? null);
        $transfer->setRecipientName($payload[static::KEY_SHIPPING_ADDRESS][static::KEY_RECIPIENT_NAME] ?? null);
        $transfer->setLine1($payload[static::KEY_SHIPPING_ADDRESS][static::KEY_LINE1] ?? null);
        $transfer->setCity($payload[static::KEY_SHIPPING_ADDRESS][static::KEY_CITY] ?? null);
        $transfer->setState($payload[static::KEY_SHIPPING_ADDRESS][static::KEY_STATE] ?? null);
        $transfer->setPostalCode($payload[static::KEY_SHIPPING_ADDRESS][static::KEY_POSTAL_CODE] ?? null);
        $transfer->setCountryCode($payload[static::KEY_SHIPPING_ADDRESS][static::KEY_COUNTRY_CODE] ?? null);

        return $transfer;
    }

    /**
     * @param PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
     * @param QuoteTransfer $quoteTransfer
     *
     * @return QuoteTransfer
     */
    public function mapPaypalExpressSuccessResponseTransferToQuoteTransfer(
        PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer,
        QuoteTransfer $quoteTransfer
    ): QuoteTransfer {
        //Nonce, payerId
        $customerTransfer = $quoteTransfer->getCustomer() ?? new CustomerTransfer();
        $shippingAddressTransfer = $quoteTransfer->getShippingAddress() ?? new AddressTransfer();

        $quoteTransfer->setCustomer($customerTransfer);
        $quoteTransfer->setShippingAddress($shippingAddressTransfer);

        $quoteTransfer->getCustomer()->setEmail($paypalExpressSuccessResponseTransfer->getEmail());
        $quoteTransfer->getCustomer()->setFirstName($paypalExpressSuccessResponseTransfer->getFirstName());
        $quoteTransfer->getCustomer()->setLastName($paypalExpressSuccessResponseTransfer->getLastName());
        $quoteTransfer->getShippingAddress()->setFirstName($paypalExpressSuccessResponseTransfer->getFirstName());
        $quoteTransfer->getShippingAddress()->setLastName($paypalExpressSuccessResponseTransfer->getLastName());
        $quoteTransfer->getShippingAddress()->setEmail($paypalExpressSuccessResponseTransfer->getEmail());
        $quoteTransfer->getShippingAddress()->setAddress1($paypalExpressSuccessResponseTransfer->getLine1());
        $quoteTransfer->getShippingAddress()->setCity($paypalExpressSuccessResponseTransfer->getCity());
        $quoteTransfer->getShippingAddress()->setCity($paypalExpressSuccessResponseTransfer->getCity());
        $quoteTransfer->getShippingAddress()->setState($paypalExpressSuccessResponseTransfer->getState());
        $quoteTransfer->getShippingAddress()->setZipCode($paypalExpressSuccessResponseTransfer->getPostalCode());
        $quoteTransfer->getShippingAddress()->setZipCode($paypalExpressSuccessResponseTransfer->getPostalCode());
        $quoteTransfer->setBillingSameAsShipping(true);

        //TODO: Get country code
//        $quoteTransfer->getShippingAddress()->setCountry($paypalExpressSuccessResponseTransfer->getPostalCode());

        return $quoteTransfer;
    }
}