<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutPostSaveInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin as BaseAbstractPlugin;

/**
 * @method \SprykerEco\Zed\Braintree\Business\BraintreeFacadeInterface getFacade()
 */
class BraintreeCheckoutPostSaveHookPlugin extends BaseAbstractPlugin implements CheckoutPostSaveInterface
{
    /**
     * {@inheritDoc}
     * - Executes Braintree sale API call and updates order payment method data.
     * - Updates `CheckoutResponseTransfer` and `QuoteTransfer` accordingly to API response.
     * - If API request is successful - updates order payment method data according to `QuoteTransfer`.
     * - Requires `QuoteTransfer.payment` to be set.
     * - Implementations of this interface are called after the order is placed.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return void
     */
    public function executeHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse): void
    {
        $this->getFacade()->executeCheckoutPostSaveHook($quoteTransfer, $checkoutResponse);
    }
}
