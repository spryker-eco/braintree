<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction;

use Braintree\Transaction as BraintreeTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;

class RevertTransaction extends AbstractTransaction
{
    /**
     * @return string
     */
    protected function getTransactionType()
    {
        return ApiConstants::CREDIT;
    }

    /**
     * @return string
     */
    protected function getTransactionCode()
    {
        return ApiConstants::TRANSACTION_CODE_REVERSAL;
    }

    /**
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    public function doTransaction()
    {
        return $this->revert();
    }

    /**
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    protected function revert()
    {
        return BraintreeTransaction::void($this->getTransactionIdentifier());
    }
}
