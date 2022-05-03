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
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Braintree\BraintreeConfig getConfig()
 * @method \SprykerEco\Zed\Braintree\Communication\BraintreeCommunicationFactory getFactory()
 */
class BraintreeCheckoutPostSaveHookPlugin extends BaseAbstractPlugin implements CheckoutPostSaveInterface
{
    /**
     * {@inheritDoc}
     * - Requires `SaveOrderTransfer.idSalesOrder` to be set.
     * - Requires `QuoteTransfer.payment` to be set.
     * - Executes Braintree sale API request.
     * - Updates `CheckoutResponseTransfer` and `QuoteTransfer` accordingly to API response.
     * - If API request is successful - updates order payment method data according to `QuoteTransfer`.
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
