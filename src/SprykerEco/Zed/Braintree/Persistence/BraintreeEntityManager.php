<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence;

use Generated\Shared\Transfer\ItemTransfer;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionOrderItem;
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
     * @param ItemTransfer[] $itemTransfers
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
            foreach ($itemTransfers as $itemTransfer) {
                $paymentBraintreeOrderItemEntity = $this->getFactory()
                    ->createPaymentBraintreeOrderItemQuery()
                    ->findOneByFkSalesOrderItem($itemTransfer->getIdSalesOrderItem());

                if ($paymentBraintreeOrderItemEntity) {
                    $paymentBraintreeTransactionOrderItemEntity = new SpyPaymentBraintreeTransactionOrderItem();
                    $paymentBraintreeTransactionOrderItemEntity->setFkPaymentBraintreeTransactionStatusLog(
                        $paymentBraintreeTransactionStatusLogEntity->getIdPaymentBraintreeTransactionStatusLog()
                    );
                    $paymentBraintreeTransactionOrderItemEntity->setFkPaymentBraintreeOrderItem($paymentBraintreeOrderItemEntity->getIdPaymentBraintreeOrderItem());
                    $paymentBraintreeTransactionOrderItemEntity->save();
                }
            }

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
}
