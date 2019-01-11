<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Communication\Plugin\Oms\Condition;

use Generated\Shared\Transfer\OrderTransfer;

/**
 * @method \SprykerEco\Zed\Braintree\Business\BraintreeFacadeInterface getFacade()
 */
class IsCaptureApprovedPlugin extends AbstractCheckPlugin
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    protected function callFacade(OrderTransfer $orderTransfer)
    {
        return $this->getFacade()->isCaptureApproved($orderTransfer);
    }
}
