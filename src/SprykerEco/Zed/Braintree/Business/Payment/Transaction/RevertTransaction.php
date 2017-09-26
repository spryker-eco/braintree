<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Braintree\Business\Payment\Transaction;

use Braintree\Transaction as BraintreeTransaction;
use Spryker\Zed\Braintree\Business\Payment\Method\ApiConstants;

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
