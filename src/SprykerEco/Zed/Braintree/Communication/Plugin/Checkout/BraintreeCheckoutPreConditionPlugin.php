<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutPreConditionPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \SprykerEco\Zed\Braintree\Business\BraintreeFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Braintree\BraintreeConfig getConfig()
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Braintree\Communication\BraintreeCommunicationFactory getFactory()
 */
class BraintreeCheckoutPreConditionPlugin extends AbstractPlugin implements CheckoutPreConditionPluginInterface
{
    /**
     * {@inheritDoc}
     * - Requires `QuoteTransfer.payment.braintree` to be set.
     * - Returns `true` if payment provider is not Braintree.
     * - Returns `true` if Braintree has a nonce, adds an error message to `CheckoutResponseTransfer` and returns `false` otherwise.
     * - Used together with {@link \SprykerEco\Zed\Braintree\Communication\Plugin\Checkout\BraintreeCheckoutDoSaveOrderPlugin} and {@link \SprykerEco\Zed\Braintree\Communication\Plugin\Checkout\BraintreeCheckoutPostSavePlugin}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkCondition(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool {
        return $this->getFacade()->isQuotePaymentValid($quoteTransfer, $checkoutResponseTransfer);
    }
}
