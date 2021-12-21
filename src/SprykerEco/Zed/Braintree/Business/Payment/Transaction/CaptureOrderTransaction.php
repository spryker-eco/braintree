<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction;

use Braintree\Transaction as BraintreeTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;

class CaptureOrderTransaction extends AbstractTransaction
{
    /**
     * @var string
     */
    protected const ATTRIBUTE_KEY_ORDER_ID = 'orderId';

    /**
     * @return string
     */
    protected function getTransactionType(): string
    {
        return ApiConstants::SALE;
    }

    /**
     * @return string
     */
    protected function getTransactionCode(): string
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
        return BraintreeTransaction::submitForSettlement($this->getTransactionIdentifier(), null, [
            static::ATTRIBUTE_KEY_ORDER_ID => $this->transactionMetaTransfer->getOrderReference(),
        ]);
    }
}
