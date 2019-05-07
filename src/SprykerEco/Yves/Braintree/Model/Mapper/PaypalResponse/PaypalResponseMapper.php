<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Model\Mapper\PaypalResponse;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\BraintreePaymentTransfer;
use Generated\Shared\Transfer\CountryCollectionTransfer;
use Generated\Shared\Transfer\CountryTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface;
use SprykerEco\Shared\Braintree\BraintreeConfig;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToCountryClientInterface;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToPaymentClientInterface;

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
    protected const KEY_CURRENCY = 'currency';
    protected const KEY_AMOUNT = 'amount';

    /**
     * @var \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToPaymentClientInterface
     */
    protected $paymentClient;

    /**
     * @var \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToCountryClientInterface
     */
    protected $countryClient;

    /**
     * @var \Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface
     */
    protected $moneyPlugin;

    /**
     * @param \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToPaymentClientInterface $paymentClient
     * @param \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToCountryClientInterface $countryClient
     * @param \Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface $moneyPlugin
     */
    public function __construct(
        BraintreeToPaymentClientInterface $paymentClient,
        BraintreeToCountryClientInterface $countryClient,
        MoneyPluginInterface $moneyPlugin
    ) {
        $this->paymentClient = $paymentClient;
        $this->countryClient = $countryClient;
        $this->moneyPlugin = $moneyPlugin;
    }

    /**
     * @param array $payload
     *
     * @return \Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer
     */
    public function mapSuccessResponseToPaypalExpressSuccessResponseTransfer(array $payload): PaypalExpressSuccessResponseTransfer
    {
        $transfer = new PaypalExpressSuccessResponseTransfer();

        $transfer->setNonce($payload[static::KEY_NONCE] ?? null);
        $transfer->setEmail($payload[static::KEY_DETAILS][static::KEY_EMAIL] ?? null);
        $transfer->setFirstName($payload[static::KEY_DETAILS][static::KEY_FIRST_NAME] ?? null);
        $transfer->setLastName($payload[static::KEY_DETAILS][static::KEY_LAST_NAME] ?? null);
        $transfer->setPayerId($payload[static::KEY_DETAILS][static::KEY_PAYER_ID] ?? null);
        $transfer->setRecipientName($payload[static::KEY_DETAILS][static::KEY_SHIPPING_ADDRESS][static::KEY_RECIPIENT_NAME] ?? null);
        $transfer->setLine1($payload[static::KEY_DETAILS][static::KEY_SHIPPING_ADDRESS][static::KEY_LINE1] ?? null);
        $transfer->setCity($payload[static::KEY_DETAILS][static::KEY_SHIPPING_ADDRESS][static::KEY_CITY] ?? null);
        $transfer->setState($payload[static::KEY_DETAILS][static::KEY_SHIPPING_ADDRESS][static::KEY_STATE] ?? null);
        $transfer->setPostalCode($payload[static::KEY_DETAILS][static::KEY_SHIPPING_ADDRESS][static::KEY_POSTAL_CODE] ?? null);
        $transfer->setCountryCode($payload[static::KEY_DETAILS][static::KEY_SHIPPING_ADDRESS][static::KEY_COUNTRY_CODE] ?? null);
        $transfer->setCurrency($payload[static::KEY_CURRENCY] ?? null);
        $transfer->setAmount($payload[static::KEY_AMOUNT] ?? null);

        return $transfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function mapPaypalExpressSuccessResponseTransferToQuoteTransfer(
        PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer,
        QuoteTransfer $quoteTransfer
    ): QuoteTransfer {
        $this->addCustomer($quoteTransfer, $paypalExpressSuccessResponseTransfer);
        $this->addShippingAddress($quoteTransfer, $paypalExpressSuccessResponseTransfer);
        $this->addBillingTransfer($quoteTransfer, $paypalExpressSuccessResponseTransfer);
        $this->addPaymentTransfer($quoteTransfer, $paypalExpressSuccessResponseTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
     *
     * @return void
     */
    protected function addPaymentTransfer(
        QuoteTransfer $quoteTransfer,
        PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
    ): void {
        $brainTreePaymentTransfer = new BraintreePaymentTransfer();
        $brainTreePaymentTransfer->setNonce($paypalExpressSuccessResponseTransfer->getNonce());
        $brainTreePaymentTransfer->setBillingAddress($quoteTransfer->getBillingAddress());
        $brainTreePaymentTransfer->setLanguageIso2Code(strtolower($quoteTransfer->getBillingAddress()->getIso2Code()));
        $brainTreePaymentTransfer->setCurrencyIso3Code($paypalExpressSuccessResponseTransfer->getCurrency());
        $brainTreePaymentTransfer->setEmail($quoteTransfer->getShippingAddress()->getEmail());

        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setPaymentProvider(BraintreeConfig::PROVIDER_NAME);
        $paymentTransfer->setBraintreePayPalExpress($brainTreePaymentTransfer);
        $paymentTransfer->setBraintree($brainTreePaymentTransfer);
        $paymentTransfer->setPaymentSelection(PaymentTransfer::BRAINTREE_PAY_PAL_EXPRESS);
        $paymentTransfer->setAmount($this->moneyPlugin->convertDecimalToInteger($paypalExpressSuccessResponseTransfer->getAmount()));

        $quoteTransfer->setPayment($paymentTransfer);
    }

    /**
     * @param string $iso2Code
     *
     * @return \Generated\Shared\Transfer\CountryTransfer|null
     */
    protected function findCountryTransfer(string $iso2Code): ?CountryTransfer
    {
        $countryTransfer = (new CountryTransfer())
            ->setIso2Code($iso2Code);

        $countryCollectionTransfer = new CountryCollectionTransfer();
        $countryCollectionTransfer->addCountries($countryTransfer);
        $countryCollectionTransfer = $this->countryClient->findCountriesByIso2Codes($countryCollectionTransfer);

        return array_shift($countryCollectionTransfer->getCountries());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
     *
     * @return void
     */
    protected function addShippingAddress(
        QuoteTransfer $quoteTransfer,
        PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
    ): void {
        if (!$quoteTransfer->getBillingAddress()) {
            $quoteTransfer->setBillingAddress($this->getAddressTransfer($paypalExpressSuccessResponseTransfer));
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
     *
     * @return void
     */
    protected function addBillingTransfer(
        QuoteTransfer $quoteTransfer,
        PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
    ): void {
        if (!$quoteTransfer->getBillingAddress()) {
            $quoteTransfer->setBillingAddress($this->getAddressTransfer($paypalExpressSuccessResponseTransfer));
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
     *
     * @return void
     */
    protected function addCustomer(
        QuoteTransfer $quoteTransfer,
        PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
    ): void {
        $customerTransfer = $quoteTransfer->getCustomer() ?? new CustomerTransfer();

        $quoteTransfer->getCustomer()->setEmail($paypalExpressSuccessResponseTransfer->getEmail());
        $quoteTransfer->getCustomer()->setFirstName($paypalExpressSuccessResponseTransfer->getFirstName());
        $quoteTransfer->getCustomer()->setLastName($paypalExpressSuccessResponseTransfer->getLastName());

        if (!$quoteTransfer->getCustomer()->getIdCustomer()) {
            $quoteTransfer->getCustomer()->setIsGuest(true);
        }

        $quoteTransfer->setCustomer($customerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function getAddressTransfer(
        PaypalExpressSuccessResponseTransfer $paypalExpressSuccessResponseTransfer
    ): AddressTransfer {
        $addressTransfer = new AddressTransfer();
        $addressTransfer->fromArray($paypalExpressSuccessResponseTransfer->toArray());
        $addressTransfer->setAddress1($paypalExpressSuccessResponseTransfer->getLine1());
        $addressTransfer->setZipCode($paypalExpressSuccessResponseTransfer->getPostalCode());

        $countryTransfer = $this->findCountryTransfer($paypalExpressSuccessResponseTransfer->getCountryCode());
        if ($countryTransfer) {
            $addressTransfer->setCountry($countryTransfer);
            $addressTransfer->setIso2Code($countryTransfer->getIso2Code());
        }

        return $addressTransfer;
    }
}
