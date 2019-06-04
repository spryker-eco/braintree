<?php


namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;


use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\TransactionMetaTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Shared\Shipment\ShipmentConstants;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface;
use SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface;

class RefundItemsTransactionHandler extends AbstractTransactionHandler implements RefundItemsTransactionHandlerInterface
{
    protected const KEY_AMOUNT = 'amount';
    protected const KEY_PAYMENT_ID = 'payment_id';

    /**
     * @var \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @var BraintreeRepositoryInterface
     */
    protected $braintreeRepository;

    /**
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface $transaction
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface $transactionMetaVisitor
     * @param \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface $refundFacade
     * @param BraintreeRepositoryInterface $braintreeRepository
     */
    public function __construct(
        TransactionInterface $transaction,
        TransactionMetaVisitorInterface $transactionMetaVisitor,
        BraintreeToRefundFacadeInterface $refundFacade,
        BraintreeRepositoryInterface $braintreeRepository
    ) {
        parent::__construct($transaction, $transactionMetaVisitor);

        $this->refundFacade = $refundFacade;
        $this->braintreeRepository = $braintreeRepository;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     *
     * @return void
     */
    public function refund(array $salesOrderItems, SpySalesOrder $salesOrderEntity): void
    {
        $refundTransfer = $this->getRefund($salesOrderItems, $salesOrderEntity);
        $transactionMetaTransfer = $this->getTransactionMetaTransfer($salesOrderItems, $salesOrderEntity, $refundTransfer);

        $orderItemsGroupedByTransaction = $this->getOrderItemsGroupedByTransaction($transactionMetaTransfer);


        foreach ($orderItemsGroupedByTransaction as $transactionId => $data) {
            $transactionMetaTransfer->setIdPayment($data[static::KEY_PAYMENT_ID]);
            $transactionMetaTransfer->setTransactionIdentifier($transactionId);
            $transactionMetaTransfer->setRefundAmount($data[static::KEY_AMOUNT]);

            $braintreeTransactionResponseTransfer = $this->transaction->executeTransaction($transactionMetaTransfer);

            if ($braintreeTransactionResponseTransfer->getIsSuccess()) {
                $this->refundFacade->saveRefund($refundTransfer);
                $refundTransfer = $this->removeShipmentExpense($refundTransfer);
            }
        }
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function getRefund(array $salesOrderItems, SpySalesOrder $salesOrderEntity): RefundTransfer
    {
        return $this->refundFacade->calculateRefund($salesOrderItems, $salesOrderEntity);
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $orderItems
     *
     * @return \ArrayObject
     */
    protected function getItemsForRefunding(array $orderItems): ArrayObject
    {
        $itemsForRefunding = [];

        foreach ($orderItems as $orderItem) {
            $itemTransfer = new ItemTransfer();
            $itemTransfer->fromArray($orderItem->toArray(), true);
            $itemsForRefunding[] = $itemTransfer;
        }

        return $this->getRefundedUniqItems($itemsForRefunding);
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     * @param RefundTransfer $refundTransfer
     *
     * @return TransactionMetaTransfer
     */
    protected function getTransactionMetaTransfer(array $salesOrderItems, SpySalesOrder $salesOrderEntity, RefundTransfer $refundTransfer): TransactionMetaTransfer
    {
        $transactionMetaTransfer = new TransactionMetaTransfer();
        $transactionMetaTransfer->setIdSalesOrder($salesOrderEntity->getIdSalesOrder());
        $transactionMetaTransfer->setRefund($refundTransfer);
        $transactionMetaTransfer->setItems($this->getItemsForRefunding($salesOrderItems));

        return $transactionMetaTransfer;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[]
     */
    protected function getRefundedUniqItems(array $itemTransfers): iterable
    {
        $uniqItems = [];
        $uniqItemIds = [];

        foreach ($itemTransfers as $itemTransfer) {
            if (!in_array($itemTransfer->getIdSalesOrderItem(), $uniqItemIds)) {
                $uniqItems[] = $itemTransfer;
                $uniqItemIds[] = $uniqItemIds;
            }
        }

        $uniqItems = new ArrayObject($uniqItems);

        return $uniqItems;
    }

    /**
     * @param TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return array
     */
    protected function getOrderItemsGroupedByTransaction(TransactionMetaTransfer $transactionMetaTransfer): array
    {
        $itemsByTransactions = [];

        foreach ($transactionMetaTransfer->getItems() as $itemTransfer) {
            $paymentBraintreeOrderItemTransfer = $this->braintreeRepository
                ->findPaymentBraintreeOrderItemByIdSalesOrderItem($itemTransfer->getIdSalesOrderItem());

            $paymentBraintreeTransactionSatusLogTransfer = $this->braintreeRepository
                ->findPaymentBraintreeTransactionStatusLogQueryByPaymentBraintreeOrderItem($paymentBraintreeOrderItemTransfer->getIdPaymentBraintreeOrderItem());

            $amount = $itemsByTransactions[$paymentBraintreeTransactionSatusLogTransfer->getTransactionId()][static::KEY_AMOUNT] ?? 0;

            $itemsByTransactions[$paymentBraintreeTransactionSatusLogTransfer->getTransactionId()] = [
                static::KEY_PAYMENT_ID => $paymentBraintreeOrderItemTransfer->getFkPaymentBraintree(),
                static::KEY_AMOUNT => $amount + $itemTransfer->getPriceToPayAggregation(),
            ];
        }

        return $itemsByTransactions;
    }

    /**
     * @param RefundTransfer $refundTransfer
     *
     * @return RefundTransfer
     */
    protected function removeShipmentExpense(RefundTransfer $refundTransfer): RefundTransfer
    {
        $expenses = [];

        foreach ($refundTransfer->getExpenses() as $expenseTransfer) {
            if ($expenseTransfer->getType() !== ShipmentConstants::SHIPMENT_EXPENSE_TYPE) {
                $expenses[] = $expenseTransfer;
            }
        }

        $refundTransfer->setExpenses(new \ArrayObject($expenses));

        return $refundTransfer;
    }
}
