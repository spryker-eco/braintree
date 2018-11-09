<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction;

use Braintree\Transaction as BraintreeTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;

class CaptureTransaction extends AbstractTransaction
{
    /**
     * @return string
     */
    protected function getTransactionType()
    {
        return ApiConstants::SALE;
    }

    /**
     * @return string
     */
    protected function getTransactionCode()
    {
        return ApiConstants::TRANSACTION_CODE_CAPTURE;
    }

    /**
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    protected function doTransaction()
    {
        return $this->capture();
    }

    /**
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    protected function capture()
    {
        return BraintreeTransaction::submitForSettlement($this->getTransactionIdentifier());
    }
}
