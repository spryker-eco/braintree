<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;

use ArrayObject;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Generated\Shared\Transfer\TransactionMetaTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface;
use SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface;

class RefundItemsTransactionHandler extends AbstractTransactionHandler implements RefundItemsTransactionHandlerInterface
{
    /**
     * @see \Spryker\Shared\Shipment\ShipmentConfig::SHIPMENT_EXPENSE_TYPE|\Spryker\Shared\Shipment\ShipmentConstants::SHIPMENT_EXPENSE_TYPE
     *
     * @var string
     */
    protected const SHIPMENT_EXPENSE_TYPE = 'SHIPMENT_EXPENSE_TYPE';

    /**
     * @var string
     */
    protected const KEY_AMOUNT = 'amount';

    /**
     * @var string
     */
    protected const KEY_PAYMENT_ID = 'payment_id';

    /**
     * @var \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @var \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface
     */
    protected $braintreeRepository;

    /**
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface $refundItemsTranasction
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface $transactionMetaVisitor
     * @param \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface $refundFacade
     * @param \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface $braintreeRepository
     */
    public function __construct(
        TransactionInterface $refundItemsTranasction,
        TransactionMetaVisitorInterface $transactionMetaVisitor,
        BraintreeToRefundFacadeInterface $refundFacade,
        BraintreeRepositoryInterface $braintreeRepository
    ) {
        parent::__construct($refundItemsTranasction, $transactionMetaVisitor);

        $this->refundFacade = $refundFacade;
        $this->braintreeRepository = $braintreeRepository;
    }

    /**
     * @param array<\Orm\Zed\Sales\Persistence\SpySalesOrderItem> $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     *
     * @return void
     */
    public function refund(array $salesOrderItems, SpySalesOrder $salesOrderEntity): void
    {
        $refundTransfer = $this->getRefund($salesOrderItems, $salesOrderEntity);
        $transactionMetaTransfer = $this->createTransactionMetaTransfer($salesOrderItems, $salesOrderEntity, $refundTransfer);

        $orderItemsGroupedByTransaction = $this->getOrderItemsGroupedByTransaction($transactionMetaTransfer);
        $shipmentExpense = $this->findShipmentExpenseTransfer($refundTransfer);

        $this->executeTransactionByItems($orderItemsGroupedByTransaction, $transactionMetaTransfer, $refundTransfer);

        if ($shipmentExpense) {
            $refundTransfer->getExpenses()->append($shipmentExpense);
        }

        $this->refundFacade->saveRefund($refundTransfer);
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

    /**
     * @param array<\Orm\Zed\Sales\Persistence\SpySalesOrderItem> $orderItems
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

        return $this->getRefundedUniqueItems($itemsForRefunding);
    }

    /**
     * @param array<\Orm\Zed\Sales\Persistence\SpySalesOrderItem> $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     *
     * @return \Generated\Shared\Transfer\TransactionMetaTransfer
     */
    protected function createTransactionMetaTransfer(
        array $salesOrderItems,
        SpySalesOrder $salesOrderEntity,
        RefundTransfer $refundTransfer
    ): TransactionMetaTransfer {
        $transactionMetaTransfer = new TransactionMetaTransfer();
        $transactionMetaTransfer->setIdSalesOrder($salesOrderEntity->getIdSalesOrder());
        $transactionMetaTransfer->setRefund($refundTransfer);
        $transactionMetaTransfer->setItems($this->getItemsForRefunding($salesOrderItems));

        return $transactionMetaTransfer;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return \ArrayObject<\Generated\Shared\Transfer\ItemTransfer>
     */
    protected function getRefundedUniqueItems(array $itemTransfers): ArrayObject
    {
        $uniqueItemTransfers = [];
        $uniqueItemIds = [];

        foreach ($itemTransfers as $itemTransfer) {
            if (!in_array($itemTransfer->getIdSalesOrderItem(), $uniqueItemIds)) {
                $uniqueItemTransfers[] = $itemTransfer;
                $uniqueItemIds[] = $itemTransfer->getIdSalesOrderItem();
            }
        }

        $uniqueItemTransfers = new ArrayObject($uniqueItemTransfers);

        return $uniqueItemTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return array
     */
    protected function getOrderItemsGroupedByTransaction(TransactionMetaTransfer $transactionMetaTransfer): array
    {
        $itemsByTransactions = [];

        $refundedItemMap = $this->mapItemsAmount($transactionMetaTransfer->getItems());

        $paymentBraintreeOrderItemTransfers = $this->braintreeRepository
            ->findPaymentBraintreeOrderItemsByIdsSalesOrderItem(array_keys($refundedItemMap));

        foreach ($paymentBraintreeOrderItemTransfers as $paymentBraintreeOrderItemTransfer) {
            $paymentBraintreeTransactionStatusLogTransfer = $this->braintreeRepository
                ->findPaymentBraintreeTransactionStatusLogQueryByPaymentBraintreeOrderItem((int)$paymentBraintreeOrderItemTransfer->getIdPaymentBraintreeOrderItem());

            $amount = $itemsByTransactions[$paymentBraintreeTransactionStatusLogTransfer->getTransactionId()][static::KEY_AMOUNT] ?? 0;

            $itemsByTransactions[$paymentBraintreeTransactionStatusLogTransfer->getTransactionId()] = [
                static::KEY_PAYMENT_ID => $paymentBraintreeOrderItemTransfer->getFkPaymentBraintree(),
                static::KEY_AMOUNT => $amount + $refundedItemMap[$paymentBraintreeOrderItemTransfer->getFkSalesOrderItem()],
            ];
        }

        return $itemsByTransactions;
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    protected function removeShipmentExpense(RefundTransfer $refundTransfer): RefundTransfer
    {
        $expenses = [];

        foreach ($refundTransfer->getExpenses() as $expenseTransfer) {
            if ($expenseTransfer->getType() !== static::SHIPMENT_EXPENSE_TYPE) {
                $expenses[] = $expenseTransfer;
            }
        }

        $refundTransfer->setExpenses(new ArrayObject($expenses));

        return $refundTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     *
     * @return \Generated\Shared\Transfer\ExpenseTransfer|null
     */
    protected function findShipmentExpenseTransfer(RefundTransfer $refundTransfer): ?ExpenseTransfer
    {
        foreach ($refundTransfer->getExpenses() as $expenseTransfer) {
            if ($expenseTransfer->getType() === static::SHIPMENT_EXPENSE_TYPE) {
                return $expenseTransfer;
            }
        }

        return null;
    }

    /**
     * @param array $orderItemsGroupedByTransaction
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     *
     * @return void
     */
    protected function executeTransactionByItems(
        array $orderItemsGroupedByTransaction,
        TransactionMetaTransfer $transactionMetaTransfer,
        RefundTransfer $refundTransfer
    ): void {
        foreach ($orderItemsGroupedByTransaction as $transactionId => $data) {
            $transactionMetaTransfer->setIdPayment($data[static::KEY_PAYMENT_ID]);
            $transactionMetaTransfer->setTransactionIdentifier($transactionId);
            $transactionMetaTransfer->setRefundAmount($data[static::KEY_AMOUNT]);

            $braintreeTransactionResponseTransfer = $this->transaction->executeTransaction($transactionMetaTransfer);

            if ($braintreeTransactionResponseTransfer->getIsSuccess()) {
                $refundTransfer = $this->removeShipmentExpense($refundTransfer);
            }
        }
    }

    /**
     * @param \ArrayObject<\Generated\Shared\Transfer\ItemTransfer>|iterable $itemTransfers
     *
     * @return array
     */
    protected function mapItemsAmount(iterable $itemTransfers): array
    {
        $itemsAmountMap = [];

        foreach ($itemTransfers as $itemTransfer) {
            $itemsAmountMap[$itemTransfer->getIdSalesOrderItem()] = $itemTransfer->getPriceToPayAggregation();
        }

        return $itemsAmountMap;
    }
}
