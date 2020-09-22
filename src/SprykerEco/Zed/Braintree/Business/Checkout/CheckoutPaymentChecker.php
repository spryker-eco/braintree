<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Checkout;

use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Braintree\BraintreeConfig;

class CheckoutPaymentChecker implements CheckoutPaymentCheckerInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    public function isQuotePaymentValid(QuoteTransfer $quoteTransfer): bool
    {
        $paymentTransfer = $quoteTransfer
            ->requirePayment()
            ->getPayment();
        if ($paymentTransfer->getPaymentProvider() !== BraintreeConfig::PROVIDER_NAME) {
            return true;
        }

        $braintreePaymentTransfer = $paymentTransfer
            ->requireBraintree()
            ->getBraintree();
        if (!$braintreePaymentTransfer->getNonce()) {
            return false;
        }

        return true;
    }
}
