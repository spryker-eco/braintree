<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Checkout;

use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Braintree\BraintreeConfig;

class CheckoutPaymentChecker implements CheckoutPaymentCheckerInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE_INVALID_PARAMETER_NONCE = 'Parameter `NONCE` is invalid.';

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function isQuotePaymentValid(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool {
        $paymentTransfer = $quoteTransfer->getPaymentOrFail();

        if ($paymentTransfer->getPaymentProvider() !== BraintreeConfig::PROVIDER_NAME) {
            return true;
        }

        $braintreePaymentTransfer = $paymentTransfer->getBraintreeOrFail();

        if (!$braintreePaymentTransfer->getNonce()) {
            $checkoutResponseTransfer
                ->setIsSuccess(false)
                ->addError($this->createCheckoutErrorTransfer());

            return false;
        }

        return true;
    }

    /**
     * @return \Generated\Shared\Transfer\CheckoutErrorTransfer
     */
    protected function createCheckoutErrorTransfer(): CheckoutErrorTransfer
    {
        return (new CheckoutErrorTransfer())
            ->setMessage(static::ERROR_MESSAGE_INVALID_PARAMETER_NONCE);
    }
}
