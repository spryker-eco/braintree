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
     * - Get payment braintree query.
     *
     * @api
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeQuery
     */
    public function queryPayments();

    /**
     * Specification:
     * - Filters payment braintree query by `id_payment_braintree` column.
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
     * - Filters payment braintree query by `fk_sales_order` column.
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
     * - Get payment braintree transaction status logs query.
     *
     * @api
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function queryTransactionStatusLog();

    /**
     * Specification:
     * - Filters payment braintree transaction status logs query by `fk_payment_braintree` column.
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
     * - Get latest payment braintree transaction status log filtered by `fk_payment_braintree` column.
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
     * - Filters payment braintree transaction status logs query by `fk_sales_order` column.
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
     * - Get latest payment braintree transaction status logs filtered by `fk_sales_order` column.
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
     * - Get latest payment braintree transaction status logs filtered by `fk_sales_order` and `transaction_code` columns.
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
     * - Get payment braintree transaction request log query.
     *
     * @api
     *
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLogQuery
     */
    public function queryTransactionRequestLogByPaymentId($idPayment);
}
