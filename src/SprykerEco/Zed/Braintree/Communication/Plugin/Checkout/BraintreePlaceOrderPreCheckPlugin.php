<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutPlaceOrderPreCheckPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerEco\Shared\Braintree\BraintreeConfig;

/**
 * @method \SprykerEco\Zed\Braintree\Business\BraintreeFacadeInterface getFacade()
 */
class BraintreePlaceOrderPreCheckPlugin extends AbstractPlugin implements CheckoutPlaceOrderPreCheckPluginInterface
{
    /**
     * Specification:
     * - Checks a condition before the order is placed. If the condition fails, an error is added to the response transfer and 'false' is returned.
     * - Check could be passed (returns 'true') along with errors added to the checkout response.
     * - Don't use this plugin to write to a DB
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function check(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool {
        $quoteTransfer->requirePayment();
        $paymentTransfer = $quoteTransfer->getPayment();
        if ($paymentTransfer->getPaymentProvider() !== BraintreeConfig::PROVIDER_NAME) {
            return true;
        }

        $braintreeTransactionResponseTransfer = $this->getFacade()->preCheckPayment($quoteTransfer);
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

        $errorCode = $braintreeTransactionResponseTransfer->getCode() ?: 500;
        $error = new CheckoutErrorTransfer();

        $error->setErrorCode($errorCode)
            ->setMessage($braintreeTransactionResponseTransfer->getMessage());
        $checkoutResponseTransfer->addError($error);

        return false;
    }
}
