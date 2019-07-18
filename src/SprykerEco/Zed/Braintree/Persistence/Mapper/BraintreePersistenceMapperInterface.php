<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence\Mapper;

use Generated\Shared\Transfer\PaymentBraintreeOrderItemTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransactionRequestLogTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransfer;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintree;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeOrderItem;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog;

interface BraintreePersistenceMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentBraintreeTransfer $paymentBraintreeTransfer
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintree $paymentBraintreeEntity
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintree
     */
    public function mapPaymentBraintreeTransferToEntity(
        PaymentBraintreeTransfer $paymentBraintreeTransfer,
        SpyPaymentBraintree $paymentBraintreeEntity
    ): SpyPaymentBraintree;

    /**
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintree $paymentBraintreeEntity
     * @param \Generated\Shared\Transfer\PaymentBraintreeTransfer $paymentBraintreeTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransfer
     */
    public function mapEntityToPaymentBraintreeTransfer(
        SpyPaymentBraintree $paymentBraintreeEntity,
        PaymentBraintreeTransfer $paymentBraintreeTransfer
    ): PaymentBraintreeTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer $paymentBraintreeTransactionStatusLogTransfer
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog $paymentBraintreeTransactionStatusLogEntity
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog
     */
    public function mapPaymentBraintreeTransactionStatusLogTransferToEntity(
        PaymentBraintreeTransactionStatusLogTransfer $paymentBraintreeTransactionStatusLogTransfer,
        SpyPaymentBraintreeTransactionStatusLog $paymentBraintreeTransactionStatusLogEntity
    ): SpyPaymentBraintreeTransactionStatusLog;

    /**
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog $paymentBraintreeTransactionStatusLogEntity
     * @param \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer $paymentBraintreeTransactionStatusLogTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer
     */
    public function mapEntityToPaymentBraintreeTransactionStatusLogTransfer(
        SpyPaymentBraintreeTransactionStatusLog $paymentBraintreeTransactionStatusLogEntity,
        PaymentBraintreeTransactionStatusLogTransfer $paymentBraintreeTransactionStatusLogTransfer
    ): PaymentBraintreeTransactionStatusLogTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentBraintreeTransactionRequestLogTransfer $paymentBraintreeTransactionRequestLogTransfer
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog $paymentBraintreeTransactionRequestLogEntity
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog
     */
    public function mapPaymentBraintreeTransactionRequestLogTransferToEntity(
        PaymentBraintreeTransactionRequestLogTransfer $paymentBraintreeTransactionRequestLogTransfer,
        SpyPaymentBraintreeTransactionRequestLog $paymentBraintreeTransactionRequestLogEntity
    ): SpyPaymentBraintreeTransactionRequestLog;

    /**
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog $paymentBraintreeTransactionRequestLogEntity
     * @param \Generated\Shared\Transfer\PaymentBraintreeTransactionRequestLogTransfer $paymentBraintreeTransactionRequestLogTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionRequestLogTransfer
     */
    public function mapEntityToPaymentBraintreeTransactionRequestLogTransfer(
        SpyPaymentBraintreeTransactionRequestLog $paymentBraintreeTransactionRequestLogEntity,
        PaymentBraintreeTransactionRequestLogTransfer $paymentBraintreeTransactionRequestLogTransfer
    ): PaymentBraintreeTransactionRequestLogTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentBraintreeOrderItemTransfer $paymentBraintreeOrderItemTransfer
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeOrderItem $paymentBraintreeOrderItemEntity
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeOrderItem
     */
    public function mapPaymentBraintreeOrderItemTransferToEntity(
        PaymentBraintreeOrderItemTransfer $paymentBraintreeOrderItemTransfer,
        SpyPaymentBraintreeOrderItem $paymentBraintreeOrderItemEntity
    ): SpyPaymentBraintreeOrderItem;

    /**
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeOrderItem $paymentBraintreeOrderItemEntity
     * @param \Generated\Shared\Transfer\PaymentBraintreeOrderItemTransfer $paymentBraintreeOrderItemTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeOrderItemTransfer
     */
    public function mapEntityToPaymentBraintreeOrderItemTransfer(
        SpyPaymentBraintreeOrderItem $paymentBraintreeOrderItemEntity,
        PaymentBraintreeOrderItemTransfer $paymentBraintreeOrderItemTransfer
    ): PaymentBraintreeOrderItemTransfer;
}
