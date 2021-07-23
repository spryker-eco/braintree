<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence;

use Generated\Shared\Transfer\BraintreePaymentTransfer;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogToOrderItem;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreePersistenceFactory getFactory()
 */
class BraintreeEntityManager extends AbstractEntityManager implements BraintreeEntityManagerInterface
{
    /**
     * @param int $idPaymentBraintree
     * @param bool $isShipmentPaid
     *
     * @return void
     */
    public function updateIsShipmentPaidValue(int $idPaymentBraintree, bool $isShipmentPaid): void
    {
        $paymentBraintreeEntity = $this->getFactory()->createPaymentBraintreeQuery()->findOneByIdPaymentBraintree($idPaymentBraintree);

        if ($paymentBraintreeEntity) {
            $paymentBraintreeEntity->setIsShipmentPaid($isShipmentPaid);
            $paymentBraintreeEntity->save();
        }
    }

    /**
     * @param int $idPaymentBraintree
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     * @param string $transactionId
     *
     * @return void
     */
    public function addOrderItemsToTransactionLog(int $idPaymentBraintree, iterable $itemTransfers, string $transactionId): void
    {
        $paymentBraintreeTransactionStatusLogEntity = $this->getFactory()
            ->createPaymentBraintreeTransactionStatusLogQuery()
            ->filterByTransactionId($transactionId)
            ->findOneByFkPaymentBraintree($idPaymentBraintree);

        if ($paymentBraintreeTransactionStatusLogEntity) {
            $paymentBraintreeOrderItemEntities = $this->getFactory()
                ->createPaymentBraintreeOrderItemQuery()
                ->filterByFkSalesOrderItem_In($this->getSalesOrderItemIds($itemTransfers))
                ->find();

            $objectCollection = new ObjectCollection();
            $objectCollection->setModel(SpyPaymentBraintreeTransactionStatusLogToOrderItem::class);

            foreach ($paymentBraintreeOrderItemEntities as $paymentBraintreeOrderItemEntity) {
                $paymentBraintreeTransactionOrderItemEntity = new SpyPaymentBraintreeTransactionStatusLogToOrderItem();
                $paymentBraintreeTransactionOrderItemEntity->setFkPaymentBraintreeTransactionStatusLog(
                    $paymentBraintreeTransactionStatusLogEntity->getIdPaymentBraintreeTransactionStatusLog()
                );
                $paymentBraintreeTransactionOrderItemEntity->setFkPaymentBraintreeOrderItem($paymentBraintreeOrderItemEntity->getIdPaymentBraintreeOrderItem());

                $objectCollection->append($paymentBraintreeTransactionOrderItemEntity);
            }

            $objectCollection->save();
        }
    }

    /**
     * @param int $idPaymentBraintree
     * @param string $transactionId
     * @param bool $isShipmentOperation
     *
     * @return void
     */
    public function updateIsShipmentOperationValue(int $idPaymentBraintree, string $transactionId, bool $isShipmentOperation): void
    {
        $paymentBraintreeTransactionStatusLogEntity = $this->getFactory()
            ->createPaymentBraintreeTransactionStatusLogQuery()
            ->filterByTransactionId($transactionId)
            ->findOneByFkPaymentBraintree($idPaymentBraintree);

        if ($paymentBraintreeTransactionStatusLogEntity) {
            $paymentBraintreeTransactionStatusLogEntity->setIsShipmentOperation($isShipmentOperation);
            $paymentBraintreeTransactionStatusLogEntity->save();
        }
    }

    /**
     * @param int $idSalesOrder
     * @param \Generated\Shared\Transfer\BraintreePaymentTransfer $braintreePaymentTransfer
     *
     * @return void
     */
    public function updatePaymentByIdSalesOrder(int $idSalesOrder, BraintreePaymentTransfer $braintreePaymentTransfer): void
    {
        $paymentBraintreeEntity = $this
            ->getFactory()
            ->createPaymentBraintreeQuery()
            ->findOneByFkSalesOrder($idSalesOrder);

        $paymentBraintreeEntity->fromArray($braintreePaymentTransfer->toArray());
        $paymentBraintreeEntity->setFkSalesOrder($idSalesOrder);
        $paymentBraintreeEntity->save();
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return array
     */
    protected function getSalesOrderItemIds(iterable $itemTransfers): array
    {
        $salesOrderItemIds = [];

        foreach ($itemTransfers as $itemTransfer) {
            $salesOrderItemIds[] = $itemTransfer->getIdSalesOrderItem();
        }

        return $salesOrderItemIds;
    }
}
