<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Handler;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Yves\Currency\Plugin\CurrencyPluginInterface;
use SprykerEco\Shared\Braintree\BraintreeConfig as BraintreeSharedConfig;
use SprykerEco\Yves\Braintree\BraintreeConfig;
use Symfony\Component\HttpFoundation\Request;

class BraintreeHandler implements BraintreeHandlerInterface
{
    public const PAYMENT_PROVIDER = 'braintree';
    public const PAYMENT_METHOD_NONCE = 'payment_method_nonce';

    /**
     * @var array
     */
    protected static $paymentMethods = [
        BraintreeSharedConfig::PAYMENT_METHOD_PAY_PAL => 'pay_pal',
        BraintreeSharedConfig::PAYMENT_METHOD_CREDIT_CARD => 'credit_card',
    ];

    /**
     * @var \Spryker\Yves\Currency\Plugin\CurrencyPluginInterface
     */
    protected $currencyPlugin;

    /**
     * @var \SprykerEco\Yves\Braintree\BraintreeConfig $braintreeConfig
     */
    protected $braintreeConfig;

    /**
     * @param \Spryker\Yves\Currency\Plugin\CurrencyPluginInterface $currencyPlugin
     * @param \SprykerEco\Yves\Braintree\BraintreeConfig $braintreeConfig
     */
    public function __construct(CurrencyPluginInterface $currencyPlugin, BraintreeConfig $braintreeConfig)
    {
        $this->currencyPlugin = $currencyPlugin;
        $this->braintreeConfig = $braintreeConfig;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addPaymentToQuote(Request $request, QuoteTransfer $quoteTransfer)
    {
        $paymentSelection = $quoteTransfer->getPayment()->getPaymentSelection();

        $this->setPaymentProviderAndMethod($quoteTransfer, $paymentSelection);
        $this->setBraintreePayment($request, $quoteTransfer, $paymentSelection);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $paymentSelection
     *
     * @return void
     */
    protected function setPaymentProviderAndMethod(QuoteTransfer $quoteTransfer, $paymentSelection)
    {
        $quoteTransfer->getPayment()
            ->setPaymentProvider(BraintreeSharedConfig::PROVIDER_NAME)
            ->setPaymentMethod(static::$paymentMethods[$paymentSelection]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $paymentSelection
     *
     * @return void
     */
    protected function setBraintreePayment(Request $request, QuoteTransfer $quoteTransfer, $paymentSelection)
    {
        $braintreePaymentTransfer = $this->getBraintreePaymentTransfer($quoteTransfer, $paymentSelection);
        $nonce = $request->request->get(self::PAYMENT_METHOD_NONCE);

        if ($this->braintreeConfig->getFakePaymentMethodNonce()) {
            $nonce = $this->braintreeConfig->getFakePaymentMethodNonce();
        }

        if ($nonce === null) {
            return;
        }

        $billingAddress = $quoteTransfer->getBillingAddress();
        $braintreePaymentTransfer
            ->setAccountBrand(static::$paymentMethods[$paymentSelection])
            ->setBillingAddress($billingAddress)
            ->setShippingAddress($quoteTransfer->getShippingAddress())
            ->setEmail($quoteTransfer->getCustomer()->getEmail())
            ->setCurrencyIso3Code($this->getCurrency())
            ->setLanguageIso2Code($billingAddress->getIso2Code())
            ->setClientIp($request->getClientIp())
            ->setNonce($nonce);

        $quoteTransfer->getPayment()->setBraintree(clone $braintreePaymentTransfer);
    }

    /**
     * @return string
     */
    protected function getCurrency()
    {
        return $this->currencyPlugin->getCurrent()->getCode();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $paymentSelection
     *
     * @return \Generated\Shared\Transfer\BraintreePaymentTransfer
     */
    protected function getBraintreePaymentTransfer(QuoteTransfer $quoteTransfer, $paymentSelection)
    {
        $method = 'get' . ucfirst($paymentSelection);
        $braintreePaymentTransfer = $quoteTransfer->getPayment()->$method();

        return $braintreePaymentTransfer;
    }
}
