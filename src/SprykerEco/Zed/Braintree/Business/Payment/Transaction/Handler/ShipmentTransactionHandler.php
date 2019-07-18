<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\TransactionMetaTransfer;

class ShipmentTransactionHandler extends AbstractTransactionHandler implements ShipmentTransactionHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function captureShipment(TransactionMetaTransfer $transactionMetaTransfer): BraintreeTransactionResponseTransfer
    {
        $this->transactionMetaVisitor->visit($transactionMetaTransfer);

        return $this->transaction->executeTransaction($transactionMetaTransfer);
    }
}
