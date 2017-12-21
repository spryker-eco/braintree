<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Braintree\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin as BaseAbstractPlugin;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPreCheckPluginInterface;

/**
 * @method \SprykerEco\Zed\Braintree\Business\BraintreeFacadeInterface getFacade()
 */
class BraintreePreCheckPlugin extends BaseAbstractPlugin implements CheckoutPreCheckPluginInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function execute(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ) {
        $braintreeTransactionResponseTransfer = $this->getFacade()->preCheckPayment($quoteTransfer);
        $isPassed = $this->checkForErrors($braintreeTransactionResponseTransfer, $checkoutResponseTransfer);

        if (!$braintreeTransactionResponseTransfer->getIsSuccess()) {
            return false;
        }

        $quoteTransfer->getPayment()->getBraintree()
            ->setTransactionId($braintreeTransactionResponseTransfer->getTransactionId());

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
    ) {
        if ($braintreeTransactionResponseTransfer->getIsSuccess()) {
            return true;
        }

        $errorCode = $braintreeTransactionResponseTransfer->getCode() ?: 500;
        $error = new CheckoutErrorTransfer();
        $error
            ->setErrorCode($errorCode)
            ->setMessage($braintreeTransactionResponseTransfer->getMessage());
        $checkoutResponseTransfer->addError($error);

        return false;
    }
}
