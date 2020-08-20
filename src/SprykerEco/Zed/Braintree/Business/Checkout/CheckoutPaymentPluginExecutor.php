<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Checkout;

use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PreCheckTransactionHandlerInterface;

class CheckoutPaymentPluginExecutor implements CheckoutPaymentPluginExecutorInterface
{
    protected const HTTP_ERROR_SERVER = 500;

    /**
     * @var \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PreCheckTransactionHandlerInterface
     */
    protected $preCheckTransactionHandler;

    /**
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PreCheckTransactionHandlerInterface $preCheckTransactionHandler
     */
    public function __construct(
        PreCheckTransactionHandlerInterface $preCheckTransactionHandler
    ) {
        $this->preCheckTransactionHandler = $preCheckTransactionHandler;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function executePreCheckPlugin(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool {
        $quoteTransfer->requirePayment();
        $paymentTransfer = $quoteTransfer->getPayment();
        if ($paymentTransfer->getPaymentProvider() !== BraintreeConfig::PROVIDER_NAME) {
            return true;
        }

        $braintreeTransactionResponseTransfer = $this->preCheckTransactionHandler->preCheck($quoteTransfer);
        $isPassed = $this->checkForErrors($braintreeTransactionResponseTransfer, $checkoutResponseTransfer);

        if (!$braintreeTransactionResponseTransfer->getIsSuccess()) {
            return false;
        }

        $paymentTransfer->getBraintree()
            ->setTransactionId($braintreeTransactionResponseTransfer->getTransactionId());

        $paymentTransfer->setBraintreeTransactionResponse($braintreeTransactionResponseTransfer);
        $quoteTransfer->setPayment($paymentTransfer);

        return $isPassed;
    }

    /**
     * @param \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer $braintreeTransactionResponseTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    protected function checkForErrors(
        BraintreeTransactionResponseTransfer $braintreeTransactionResponseTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool {
        if ($braintreeTransactionResponseTransfer->getIsSuccess()) {
            return true;
        }

        $errorCode = $braintreeTransactionResponseTransfer->getCode() ?: static::HTTP_ERROR_SERVER;
        $checkoutErrorTransfer = new CheckoutErrorTransfer();

        $checkoutErrorTransfer->setErrorCode($errorCode)
            ->setMessage($braintreeTransactionResponseTransfer->getMessage());
        $checkoutResponseTransfer->addError($checkoutErrorTransfer);

        return false;
    }
}
