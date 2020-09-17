<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Checkout;

use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PreCheckTransactionHandlerInterface;
use Symfony\Component\HttpFoundation\Response;
use ArrayObject;

class CheckoutPaymentPluginExecutor implements CheckoutPaymentPluginExecutorInterface
{
    /**
     * @var \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PreCheckTransactionHandlerInterface
     */
    protected $preCheckTransactionHandler;

    /**
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PreCheckTransactionHandlerInterface $preCheckTransactionHandler
     */
    public function __construct(PreCheckTransactionHandlerInterface $preCheckTransactionHandler) {
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
        $paymentTransfer = $quoteTransfer
            ->requirePayment()
            ->getPayment();

        if ($paymentTransfer->getPaymentProvider() !== BraintreeConfig::PROVIDER_NAME) {
            return true;
        }

        $braintreeTransactionResponseTransfer = $this->preCheckTransactionHandler->preCheck($quoteTransfer);
        if (!$braintreeTransactionResponseTransfer->getIsSuccess()) {
            $checkoutErrorTransfer = $this->buildCheckoutErrorTransfer($braintreeTransactionResponseTransfer);
            $checkoutResponseTransfer->addError($checkoutErrorTransfer);

            return false;
        }

        $paymentTransfer->getBraintree()
            ->setTransactionId($braintreeTransactionResponseTransfer->getTransactionId());

        $paymentTransfer->setBraintreeTransactionResponse($braintreeTransactionResponseTransfer);
        $quoteTransfer->setPayment($paymentTransfer);

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer $braintreeTransactionResponseTransfer
     *
     * @return CheckoutErrorTransfer
     */
    protected function buildCheckoutErrorTransfer(BraintreeTransactionResponseTransfer $braintreeTransactionResponseTransfer): CheckoutErrorTransfer {
        $errorCode = $braintreeTransactionResponseTransfer->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;
        $checkoutErrorTransfer = new CheckoutErrorTransfer();

        $checkoutErrorTransfer->setErrorCode($errorCode)
            ->setMessage($braintreeTransactionResponseTransfer->getMessage());

        return $checkoutErrorTransfer;
    }
}
