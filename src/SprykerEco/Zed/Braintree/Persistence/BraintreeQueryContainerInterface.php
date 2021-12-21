<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence;

use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface BraintreeQueryContainerInterface extends QueryContainerInterface
{
    /**
     * Specification:
     * - TODO: write specification
     *
     * @api
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeQuery
     */
    public function queryPayments();

    /**
     * Specification:
     * - TODO: write specification
     *
     * @api
     *
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeQuery
     */
    public function queryPaymentById($idPayment);

    /**
     * Specification:
     * - TODO: write specification
     *
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeQuery
     */
    public function queryPaymentBySalesOrderId($idSalesOrder);

    /**
     * Specification:
     * - TODO: write specification
     *
     * @api
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLog();

    /**
     * Specification:
     * - TODO: write specification
     *
     * @api
     *
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogByPaymentId($idPayment);

    /**
     * Specification:
     * - TODO: write specification
     *
     * @api
     *
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogByPaymentIdLatestFirst($idPayment);

    /**
     * Specification:
     * - TODO: write specification
     *
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogBySalesOrderId($idSalesOrder);

    /**
     * Specification:
     * - TODO: write specification
     *
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogBySalesOrderIdLatestFirst($idSalesOrder);

    /**
     * Specification:
     * - TODO: write specification
     *
     * @api
     *
     * @param int $idSalesOrder
     * @param string $transactionCode
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLogBySalesOrderIdAndTransactionCodeLatestFirst($idSalesOrder, $transactionCode);

    /**
     * Specification:
     * - TODO: write specification
     *
     * @api
     *
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLogQuery
     */
    public function queryTransactionRequestLogByPaymentId($idPayment);
}
