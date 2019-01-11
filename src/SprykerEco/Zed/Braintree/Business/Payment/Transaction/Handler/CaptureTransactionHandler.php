<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\TransactionMetaTransfer;

class CaptureTransactionHandler extends AbstractTransactionHandler implements CaptureTransactionHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function capture(TransactionMetaTransfer $transactionMetaTransfer)
    {
        $this->transactionMetaVisitor->visit($transactionMetaTransfer);

        return $this->transaction->executeTransaction($transactionMetaTransfer);
    }
}
