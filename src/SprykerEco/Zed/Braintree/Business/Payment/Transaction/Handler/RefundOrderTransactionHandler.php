<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\TransactionMetaTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface;

class RefundOrderTransactionHandler extends AbstractTransactionHandler implements RefundOrderTransactionHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface $transaction
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface $transactionMetaVisitor
     * @param \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface $refundFacade
     */
    public function __construct(
        TransactionInterface $transaction,
        TransactionMetaVisitorInterface $transactionMetaVisitor,
        BraintreeToRefundFacadeInterface $refundFacade
    ) {
        parent::__construct($transaction, $transactionMetaVisitor);

        $this->refundFacade = $refundFacade;
    }

    /**
     * @param array<\Orm\Zed\Sales\Persistence\SpySalesOrderItem> $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function refund(array $salesOrderItems, SpySalesOrder $salesOrderEntity): BraintreeTransactionResponseTransfer
    {
        $refundTransfer = $this->getRefund($salesOrderItems, $salesOrderEntity);

        $transactionMetaTransfer = new TransactionMetaTransfer();
        $transactionMetaTransfer->setIdSalesOrder($salesOrderEntity->getIdSalesOrder());
        $transactionMetaTransfer->setRefund($refundTransfer);

        $this->transactionMetaVisitor->visit($transactionMetaTransfer);

        $braintreeTransactionResponseTransfer = $this->transaction->executeTransaction($transactionMetaTransfer);

        if ($braintreeTransactionResponseTransfer->getIsSuccess()) {
            $this->refundFacade->saveRefund($refundTransfer);
        }

        return $braintreeTransactionResponseTransfer;
    }

    /**
     * @param array<\Orm\Zed\Sales\Persistence\SpySalesOrderItem> $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function getRefund(array $salesOrderItems, SpySalesOrder $salesOrderEntity): RefundTransfer
    {
        return $this->refundFacade->calculateRefund($salesOrderItems, $salesOrderEntity);
    }
}
