<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TransactionMetaTransfer;

class PreCheckTransactionHandler extends AbstractTransactionHandler implements PreCheckTransactionHandlerInterface
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
