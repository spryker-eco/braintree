<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\TransactionMetaTransfer;

class AuthorizeTransactionHandler extends AbstractTransactionHandler implements AuthorizeTransactionHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function authorize(TransactionMetaTransfer $transactionMetaTransfer)
    {
        $this->transactionMetaVisitor->visit($transactionMetaTransfer);

        return $this->transaction->executeTransaction($transactionMetaTransfer);
    }
}
