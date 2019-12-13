<?php

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TransactionMetaTransfer;

class PaymentTransactionHandler extends AbstractTransactionHandler implements PaymentTransactionHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function createPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer)
    {
        $transactionMetaTransfer = new TransactionMetaTransfer();
        $transactionMetaTransfer->setQuote($quoteTransfer);
        $transactionMetaTransfer->setTransactionIdentifier('');
        $transactionMetaTransfer->setIdPayment('');

        $response = $this->transaction->executeTransaction($transactionMetaTransfer);
        $quoteTransfer->getPayment()->setBraintreeTransactionResponse($response);
        $quoteTransfer->getPayment()->getBraintree()
            ->setTransactionId($response->getTransactionId());

        $checkoutResponseTransfer->setIsSuccess($response->getIsSuccess());

        return $quoteTransfer;
    }
}
