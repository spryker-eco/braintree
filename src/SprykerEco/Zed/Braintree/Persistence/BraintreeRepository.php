<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence;

use Generated\Shared\Transfer\PaymentBraintreeOrderItemTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreePersistenceFactory getFactory()
 */
class BraintreeRepository extends AbstractRepository implements BraintreeRepositoryInterface
{
    /**
     * @param int $idPaymentBraintree
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransfer|null
     */
    public function findPaymentBraintreeById(int $idPaymentBraintree): ?PaymentBraintreeTransfer
    {
        $paymentBraintreeEntity = $this->getFactory()
            ->createPaymentBraintreeQuery()
            ->findOneByIdPaymentBraintree($idPaymentBraintree);

        if ($paymentBraintreeEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createBraintreePersistenceMapper()
            ->mapEntityToPaymentBraintreeTransfer($paymentBraintreeEntity, new PaymentBraintreeTransfer());
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransfer|null
     */
    public function findPaymentBraintreeBySalesOrderId(int $idSalesOrder): ?PaymentBraintreeTransfer
    {
        $paymentBraintreeEntity = $this->getFactory()
            ->createPaymentBraintreeQuery()
            ->findOneByFkSalesOrder($idSalesOrder);

        if ($paymentBraintreeEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createBraintreePersistenceMapper()
            ->mapEntityToPaymentBraintreeTransfer($paymentBraintreeEntity, new PaymentBraintreeTransfer());
    }

    /**
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeOrderItemTransfer|null
     */
    public function findPaymentBraintreeOrderItemByIdSalesOrderItem(int $idSalesOrderItem): ?PaymentBraintreeOrderItemTransfer
    {
        $paymentBraintreeOrderItemEntity = $this->getFactory()
            ->createPaymentBraintreeOrderItemQuery()
            ->findOneByFkSalesOrderItem($idSalesOrderItem);

        if ($paymentBraintreeOrderItemEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createBraintreePersistenceMapper()
            ->mapEntityToPaymentBraintreeOrderItemTransfer($paymentBraintreeOrderItemEntity, new PaymentBraintreeOrderItemTransfer());
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findPaymentBraintreeTransactionStatusLogQueryBySalesOrderId(int $idSalesOrder): ?PaymentBraintreeTransactionStatusLogTransfer
    {
        $paymentBraintreeTransactionStatusLogEntity = $this->getFactory()
            ->createPaymentBraintreeTransactionStatusLogQuery()
            ->useSpyPaymentBraintreeQuery()
            ->filterByFkSalesOrder($idSalesOrder)
            ->endUse()
            ->findOne();

        if ($paymentBraintreeTransactionStatusLogEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createBraintreePersistenceMapper()
            ->mapEntityToPaymentBraintreeTransactionStatusLogTransfer($paymentBraintreeTransactionStatusLogEntity, new PaymentBraintreeTransactionStatusLogTransfer());
    }

    /**
     * @param int $idSalesOrder
     * @param string $transactionCode
     * @param string|array $statusCode
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findSucceededPaymentBraintreeTransactionStatusLogQueryBySalesOrderIdAndTransactionCode(
        int $idSalesOrder,
        string $transactionCode,
        $statusCode
    ): ?PaymentBraintreeTransactionStatusLogTransfer {
        $paymentBraintreeTransactionStatusLogEntity = $this->getFactory()
            ->createPaymentBraintreeTransactionStatusLogQuery()
            ->filterByTransactionCode($transactionCode)
            ->filterByTransactionStatus((array)$statusCode, Criteria::IN)
            ->filterByIsShipmentOperation(false)
            ->useSpyPaymentBraintreeQuery()
                ->filterByFkSalesOrder($idSalesOrder)
            ->endUse()
            ->filterByIsSuccess(true)
            ->findOne();

        if ($paymentBraintreeTransactionStatusLogEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createBraintreePersistenceMapper()
            ->mapEntityToPaymentBraintreeTransactionStatusLogTransfer($paymentBraintreeTransactionStatusLogEntity, new PaymentBraintreeTransactionStatusLogTransfer());
    }

    /**
     * @param int $idPaymentBraintreeOrderItem
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findPaymentBraintreeTransactionStatusLogQueryByPaymentBraintreeOrderItem(
        int $idPaymentBraintreeOrderItem
    ): ?PaymentBraintreeTransactionStatusLogTransfer {
        $paymentBraintreeTransactionStatusLogEntity = $this->getFactory()
            ->createPaymentBraintreeTransactionStatusLogQuery()
            ->useSpyPaymentBraintreeTransactionOrderItemQuery()
                ->filterByFkPaymentBraintreeOrderItem($idPaymentBraintreeOrderItem)
            ->endUse()
            ->findOne();

        if ($paymentBraintreeTransactionStatusLogEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createBraintreePersistenceMapper()
            ->mapEntityToPaymentBraintreeTransactionStatusLogTransfer($paymentBraintreeTransactionStatusLogEntity, new PaymentBraintreeTransactionStatusLogTransfer());
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findTransactionRequestLogByIdSalesOrderForShipment(int $idSalesOrder): ?PaymentBraintreeTransactionStatusLogTransfer
    {
        $paymentBraintreeTransactionStatusLogEntity = $this->getFactory()
            ->createPaymentBraintreeTransactionStatusLogQuery()
            ->useSpyPaymentBraintreeQuery()
                ->filterByFkSalesOrder($idSalesOrder)
            ->endUse()
            ->filterByIsShipmentOperation(true)
            ->findOne();

        if ($paymentBraintreeTransactionStatusLogEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createBraintreePersistenceMapper()
            ->mapEntityToPaymentBraintreeTransactionStatusLogTransfer($paymentBraintreeTransactionStatusLogEntity, new PaymentBraintreeTransactionStatusLogTransfer());
    }
}
