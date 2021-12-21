<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence;

use Generated\Shared\Transfer\PaymentBraintreeOrderItemTransfer;
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
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeOrderItemTransfer|null
     */
    public function findPaymentBraintreeOrderItemByIdSalesOrderItem(int $idSalesOrderItem): ?PaymentBraintreeOrderItemTransfer;

    /**
     * @param array $idsSalesOrderItem
     *
     * @return array<\Generated\Shared\Transfer\PaymentBraintreeOrderItemTransfer>
     */
    public function findPaymentBraintreeOrderItemsByIdsSalesOrderItem(array $idsSalesOrderItem): array;

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findPaymentBraintreeTransactionStatusLogQueryBySalesOrderId(int $idSalesOrder): ?PaymentBraintreeTransactionStatusLogTransfer;

    /**
     * @param int $idSalesOrder
     * @param string $transactionCode
     * @param array|string $statusCode
     *
     * @return bool
     */
    public function isSucceededPaymentBraintreeTransactionStatusLogQueryExistBySalesOrderIdAndTransactionCode(
        int $idSalesOrder,
        string $transactionCode,
        $statusCode
    ): bool;

    /**
     * @param int $idPaymentBraintreeOrderItem
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findPaymentBraintreeTransactionStatusLogQueryByPaymentBraintreeOrderItem(
        int $idPaymentBraintreeOrderItem
    ): ?PaymentBraintreeTransactionStatusLogTransfer;

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    public function findTransactionRequestLogByIdSalesOrderForShipment(int $idSalesOrder): ?PaymentBraintreeTransactionStatusLogTransfer;
}
