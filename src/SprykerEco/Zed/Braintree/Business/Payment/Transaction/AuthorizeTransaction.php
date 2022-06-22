<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction;

use Braintree\Exception\NotFound;
use Braintree\Result\Error;
use Braintree\Result\Successful;
use Braintree\Transaction as BraintreeTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;

class AuthorizeTransaction extends AbstractTransaction
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
        return ApiConstants::TRANSACTION_CODE_AUTHORIZE;
    }

    /**
     * @return \Braintree\Result\Successful|\Braintree\Result\Error|\Braintree\Transaction
     */
    public function doTransaction()
    {
        return $this->authorize();
    }

    /**
     * @param \Braintree\Transaction $response
     *
     * @return bool
     */
    protected function isTransactionSuccessful($response)
    {
        return ($response->success && $response->transaction->__get('processorResponseCode') === ApiConstants::PAYMENT_CODE_AUTHORIZE_SUCCESS);
    }

    /**
     * @return \Braintree\Result\Successful|\Braintree\Result\Error
     */
    protected function authorize()
    {
        try {
            $transaction = $this->findTransaction();
        } catch (NotFound $e) {
            $message = sprintf('Could not find payment with the transaction id "%s"', $this->getTransactionIdentifier());

            return new Error(['message' => $message, 'errors' => []]);
        }

        return new Successful([$transaction]);
    }

    /**
     * @return \Braintree\Transaction
     */
    protected function findTransaction()
    {
        return BraintreeTransaction::find($this->getTransactionIdentifier());
    }
}
