<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Hook;

use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Order\SaverInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PaymentTransactionHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

class CheckoutPostSaveHook implements CheckoutPostSaveHookInterface
{
    /**
     * @var \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PaymentTransactionHandlerInterface
     */
    protected $paymentTransactionHandler;

    /**
     * @var \SprykerEco\Zed\Braintree\Business\Order\SaverInterface
     */
    protected $orderSaver;

    /**
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PaymentTransactionHandlerInterface $paymentTransactionHandler
     * @param \SprykerEco\Zed\Braintree\Business\Order\SaverInterface $orderSaver
     */
    public function __construct(
        PaymentTransactionHandlerInterface $paymentTransactionHandler,
        SaverInterface $orderSaver
    ) {
        $this->paymentTransactionHandler = $paymentTransactionHandler;
        $this->orderSaver = $orderSaver;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function executeCheckoutPostSaveHook(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): CheckoutResponseTransfer {
        if ($quoteTransfer->getPaymentOrFail()->getPaymentProvider() !== BraintreeConfig::PROVIDER_NAME) {
            return $checkoutResponseTransfer;
        }

        $quoteTransfer = $this->paymentTransactionHandler->createPayment($quoteTransfer, $checkoutResponseTransfer);

        $braintreeTransactionResponseTransfer = $quoteTransfer->getPaymentOrFail()->getBraintreeTransactionResponseOrFail();
        if (!$braintreeTransactionResponseTransfer->getIsSuccess()) {
            $checkoutErrorTransfer = $this->createCheckoutErrorTransfer($braintreeTransactionResponseTransfer);
            $checkoutResponseTransfer->addError($checkoutErrorTransfer);

            return $checkoutResponseTransfer;
        }

        $this->orderSaver->updateOrderPayment($quoteTransfer, $checkoutResponseTransfer->getSaveOrder());

        return $checkoutResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer $braintreeTransactionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutErrorTransfer
     */
    protected function createCheckoutErrorTransfer(BraintreeTransactionResponseTransfer $braintreeTransactionResponseTransfer): CheckoutErrorTransfer
    {
        $errorCode = $braintreeTransactionResponseTransfer->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;

        return (new CheckoutErrorTransfer())
            ->setErrorCode($errorCode)
            ->setMessage($braintreeTransactionResponseTransfer->getMessage());
    }
}
