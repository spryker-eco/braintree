<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Communication\Plugin\Oms\Condition;

use Generated\Shared\Transfer\OrderTransfer;

/**
 * @method \SprykerEco\Zed\Braintree\Business\BraintreeFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Braintree\BraintreeConfig getConfig()
 * @method \SprykerEco\Zed\Braintree\Communication\BraintreeCommunicationFactory getFactory()
 */
class IsRefundApprovedPlugin extends AbstractCheckPlugin
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    protected function callFacade(OrderTransfer $orderTransfer)
    {
        return $this->getFacade()->isRefundApproved($orderTransfer);
    }
}
