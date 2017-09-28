<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TransactionMetaTransfer;

class PreCheckTransactionHandler extends AbstractTransactionHandler
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function preCheck(QuoteTransfer $quoteTransfer)
    {
        $transactionMetaTransfer = new TransactionMetaTransfer();
        $transactionMetaTransfer->setQuote($quoteTransfer);
        $transactionMetaTransfer->setTransactionIdentifier('');
        $transactionMetaTransfer->setIdPayment('');

        return $this->transaction->executeTransaction($transactionMetaTransfer);
    }

}
