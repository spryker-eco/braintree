<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence;

use Orm\Zed\Braintree\Persistence\Map\SpyPaymentBraintreeTransactionRequestLogTableMap;
use Orm\Zed\Braintree\Persistence\Map\SpyPaymentBraintreeTransactionStatusLogTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Propel;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreePersistenceFactory getFactory()
 */
class BraintreeQueryContainer extends AbstractQueryContainer implements BraintreeQueryContainerInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeQuery
     */
    public function queryPayments()
    {
        return $this->getFactory()->createPaymentBraintreeQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeQuery
     */
    public function queryPaymentById($idPayment)
    {
        return $this
            ->queryPayments()
            ->filterByIdPaymentBraintree($idPayment);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeQuery
     */
    public function queryPaymentBySalesOrderId($idSalesOrder)
    {
        return $this
            ->queryPayments()
            ->filterByFkSalesOrder($idSalesOrder);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLog()
    {
        return $this->getFactory()->createPaymentBraintreeTransactionStatusLogQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogByPaymentId($idPayment)
    {
        return $this
            ->queryTransactionStatusLog()
            ->filterByFkPaymentBraintree($idPayment);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogByPaymentIdLatestFirst($idPayment)
    {
        return $this
            ->queryTransactionStatusLogByPaymentId($idPayment)
            ->orderByIdPaymentBraintreeTransactionStatusLog(Criteria::DESC);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogBySalesOrderId($idSalesOrder)
    {
        return $this
            ->queryTransactionStatusLog()
            ->useSpyPaymentBraintreeQuery()
                ->filterByFkSalesOrder($idSalesOrder)
            ->endUse();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogBySalesOrderIdLatestFirst($idSalesOrder)
    {
        return $this
            ->queryTransactionStatusLogBySalesOrderId($idSalesOrder)
            ->orderByIdPaymentBraintreeTransactionStatusLog(Criteria::DESC);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idSalesOrder
     * @param string $transactionCode
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogBySalesOrderIdAndTransactionCodeLatestFirst($idSalesOrder, $transactionCode)
    {
        return $this->queryTransactionStatusLogBySalesOrderIdLatestFirst($idSalesOrder)
            ->withColumn(SpyPaymentBraintreeTransactionRequestLogTableMap::COL_TRANSACTION_CODE)
            ->addJoin(
                [
                    SpyPaymentBraintreeTransactionStatusLogTableMap::COL_TRANSACTION_ID,
                    SpyPaymentBraintreeTransactionRequestLogTableMap::COL_TRANSACTION_CODE,
                ],
                [
                    SpyPaymentBraintreeTransactionRequestLogTableMap::COL_TRANSACTION_ID,
                    Propel::getConnection()->quote($transactionCode),
                ],
            );
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLogQuery
     */
    public function queryTransactionRequestLogByPaymentId($idPayment)
    {
        $query = $this->getFactory()->createPaymentBraintreeTransactionRequestLogQuery();

        return $query->filterByFkPaymentBraintree($idPayment);
    }
}
