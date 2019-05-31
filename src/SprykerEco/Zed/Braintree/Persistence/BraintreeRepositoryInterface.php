<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence;

use Generated\Shared\Transfer\PaymentBraintreeTransactionRequestLogTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransfer;

interface BraintreeRepositoryInterface
{
    /**
     * @param int $idPaymentBraintree
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransfer|null
     */
    public function findPaymentBraintreeById(int $idPaymentBraintree): ?PaymentBraintreeTransfer;

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransfer|null
     */
    public function findPaymentBraintreeBySalesOrderId(int $idSalesOrder): ?PaymentBraintreeTransfer;

    /**
     * @param int $idPaymentBraintree
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findPaymentBraintreeTransactionStatusLogQueryByPaymentBraintreeId(int $idPaymentBraintree): ?PaymentBraintreeTransactionStatusLogTransfer;

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findPaymentBraintreeTransactionStatusLogQueryBySalesOrderId(int $idSalesOrder): ?PaymentBraintreeTransactionStatusLogTransfer;

    /**
     * @param int $idSalesOrder
     * @param string $transactionCode
     * @param string|array $statusCode
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findPaymentBraintreeTransactionStatusLogQueryBySalesOrderIdAndTransactionCodeLatestFirst(
        int $idSalesOrder,
        string $transactionCode,
        $statusCode
    ): ?PaymentBraintreeTransactionStatusLogTransfer;

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
    ): ?PaymentBraintreeTransactionStatusLogTransfer;

    /**
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findPaymentBraintreeTransactionStatusLogQueryByOrderItem(
        int $idSalesOrderItem
    ): ?PaymentBraintreeTransactionStatusLogTransfer;

    /**
     * @param int $idPaymentBraintree
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionRequestLogTransfer|null
     */
    public function findTransactionRequestLogByPaymentBraintreeId(int $idPaymentBraintree): ?PaymentBraintreeTransactionRequestLogTransfer;

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findTransactionRequestLogByIdSalesOrderForShipment(int $idSalesOrder): ?PaymentBraintreeTransactionStatusLogTransfer;
}
