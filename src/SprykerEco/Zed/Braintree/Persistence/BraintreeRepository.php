<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence;

use Generated\Shared\Transfer\PaymentBraintreeTransactionRequestLogTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransfer;
use Orm\Zed\Braintree\Persistence\Map\SpyPaymentBraintreeTransactionRequestLogTableMap;
use Orm\Zed\Braintree\Persistence\Map\SpyPaymentBraintreeTransactionStatusLogTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Propel;
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
     * @param int $idPaymentBraintree
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findPaymentBraintreeTransactionStatusLogQueryByPaymentBraintreeId(int $idPaymentBraintree): ?PaymentBraintreeTransactionStatusLogTransfer
    {
        $paymentBraintreeTransactionStatusLogEntity = $this->getFactory()
            ->createPaymentBraintreeTransactionStatusLogQuery()
            ->findOneByFkPaymentBraintree($idPaymentBraintree);

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
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer
     */
    public function findPaymentBraintreeTransactionStatusLogQueryBySalesOrderIdAndTransactionCodeLatestFirst(
        int $idSalesOrder,
        string $transactionCode,
        $statusCode
    ): PaymentBraintreeTransactionStatusLogTransfer {
        $paymentBraintreeTransactionStatusLogEntity = $this->getFactory()
            ->createPaymentBraintreeTransactionStatusLogQuery()
            ->useSpyPaymentBraintreeQuery()
                ->filterByFkSalesOrder($idSalesOrder)
            ->endUse()
            ->orderByIdPaymentBraintreeTransactionStatusLog(Criteria::DESC)
            ->withColumn(SpyPaymentBraintreeTransactionRequestLogTableMap::COL_TRANSACTION_CODE)
            ->addJoin(
                [
                    SpyPaymentBraintreeTransactionStatusLogTableMap::COL_TRANSACTION_ID,
                    SpyPaymentBraintreeTransactionRequestLogTableMap::COL_TRANSACTION_CODE,
                ],
                [
                    SpyPaymentBraintreeTransactionRequestLogTableMap::COL_TRANSACTION_ID,
                    Propel::getConnection()->quote($transactionCode),
                ]
            )
            ->filterByTransactionStatus((array)$statusCode, Criteria::IN)
            ->findOne();

        if ($paymentBraintreeTransactionStatusLogEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createBraintreePersistenceMapper()
            ->mapEntityToPaymentBraintreeTransactionStatusLogTransfer($paymentBraintreeTransactionStatusLogEntity, new PaymentBraintreeTransactionStatusLogTransfer());
    }

    /**
     * @param int $idPaymentBraintree
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionRequestLogTransfer|null
     */
    public function findTransactionRequestLogByPaymentBraintreeId(int $idPaymentBraintree): ?PaymentBraintreeTransactionRequestLogTransfer
    {
        $paymentBraintreeTransactionRequestLogEntity = $this->getFactory()
            ->createPaymentBraintreeTransactionRequestLogQuery()
            ->findOneByFkPaymentBraintree($idPaymentBraintree);

        if ($paymentBraintreeTransactionRequestLogEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createBraintreePersistenceMapper()
            ->mapEntityToPaymentBraintreeTransactionRequestLogTransfer($paymentBraintreeTransactionRequestLogEntity, new PaymentBraintreeTransactionRequestLogTransfer());
    }
}
