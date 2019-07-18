<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Filter;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Braintree\BraintreeConfig;

class PaypalExpressPaymentMethodFilter implements PaypalExpressPaymentMethodFilterInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function filterPaymentMethods(PaymentMethodsTransfer $paymentMethodsTransfer, QuoteTransfer $quoteTransfer): PaymentMethodsTransfer
    {
        $result = new ArrayObject();

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethod) {
            if ($paymentMethod->getMethodName() === BraintreeConfig::PAYMENT_METHOD_PAY_PAL_EXPRESS
                && !$this->isPaymenMethodBraintreePayPalExpressSelected($quoteTransfer)
            ) {
                continue;
            }
            $result->append($paymentMethod);
        }

        $paymentMethodsTransfer->setMethods($result);

        return $paymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function isPaymenMethodBraintreePayPalExpressSelected(QuoteTransfer $quoteTransfer): bool
    {
        return $quoteTransfer->getPayment()
            && $quoteTransfer->getPayment()->getPaymentSelection() === BraintreeConfig::PAYMENT_METHOD_PAY_PAL_EXPRESS;
    }
}
