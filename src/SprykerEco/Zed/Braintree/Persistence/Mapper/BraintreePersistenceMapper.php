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

class BraintreePersistenceMapper implements BraintreePersistenceMapperInterface
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
    ): SpyPaymentBraintree {
        $paymentBraintreeEntity->fromArray($paymentBraintreeTransfer->modifiedToArray());
        $paymentBraintreeEntity->setNew($paymentBraintreeTransfer->getIdPaymentBraintree() === null);

        return $paymentBraintreeEntity;
    }

    /**
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintree $paymentBraintreeEntity
     * @param \Generated\Shared\Transfer\PaymentBraintreeTransfer $paymentBraintreeTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransfer
     */
    public function mapEntityToPaymentBraintreeTransfer(
        SpyPaymentBraintree $paymentBraintreeEntity,
        PaymentBraintreeTransfer $paymentBraintreeTransfer
    ): PaymentBraintreeTransfer {
        return $paymentBraintreeTransfer->fromArray($paymentBraintreeEntity->toArray(), true);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer $paymentBraintreeTransactionStatusLogTransfer
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog $paymentBraintreeTransactionStatusLogEntity
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog
     */
    public function mapPaymentBraintreeTransactionStatusLogTransferToEntity(
        PaymentBraintreeTransactionStatusLogTransfer $paymentBraintreeTransactionStatusLogTransfer,
        SpyPaymentBraintreeTransactionStatusLog $paymentBraintreeTransactionStatusLogEntity
    ): SpyPaymentBraintreeTransactionStatusLog {
        $paymentBraintreeTransactionStatusLogEntity->fromArray($paymentBraintreeTransactionStatusLogTransfer->modifiedToArray());
        $paymentBraintreeTransactionStatusLogEntity->setNew($paymentBraintreeTransactionStatusLogTransfer->getIdPaymentBraintreeTransactionStatusLog() === null);

        return $paymentBraintreeTransactionStatusLogEntity;
    }

    /**
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog $paymentBraintreeTransactionStatusLogEntity
     * @param \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer $paymentBraintreeTransactionStatusLogTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer
     */
    public function mapEntityToPaymentBraintreeTransactionStatusLogTransfer(
        SpyPaymentBraintreeTransactionStatusLog $paymentBraintreeTransactionStatusLogEntity,
        PaymentBraintreeTransactionStatusLogTransfer $paymentBraintreeTransactionStatusLogTransfer
    ): PaymentBraintreeTransactionStatusLogTransfer {
        return $paymentBraintreeTransactionStatusLogTransfer->fromArray($paymentBraintreeTransactionStatusLogEntity->toArray(), true);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentBraintreeTransactionRequestLogTransfer $paymentBraintreeTransactionRequestLogTransfer
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog $paymentBraintreeTransactionRequestLogEntity
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog
     */
    public function mapPaymentBraintreeTransactionRequestLogTransferToEntity(
        PaymentBraintreeTransactionRequestLogTransfer $paymentBraintreeTransactionRequestLogTransfer,
        SpyPaymentBraintreeTransactionRequestLog $paymentBraintreeTransactionRequestLogEntity
    ): SpyPaymentBraintreeTransactionRequestLog {
        $paymentBraintreeTransactionRequestLogEntity->fromArray($paymentBraintreeTransactionRequestLogTransfer->modifiedToArray());
        $paymentBraintreeTransactionRequestLogEntity->setNew($paymentBraintreeTransactionRequestLogTransfer->getIdPaymentBraintreeTransactionRequestLog() === null);

        return $paymentBraintreeTransactionRequestLogEntity;
    }

    /**
     * @param \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog $paymentBraintreeTransactionRequestLogEntity
     * @param \Generated\Shared\Transfer\PaymentBraintreeTransactionRequestLogTransfer $paymentBraintreeTransactionRequestLogTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionRequestLogTransfer
     */
    public function mapEntityToPaymentBraintreeTransactionRequestLogTransfer(
        SpyPaymentBraintreeTransactionRequestLog $paymentBraintreeTransactionRequestLogEntity,
        PaymentBraintreeTransactionRequestLogTransfer $paymentBraintreeTransactionRequestLogTransfer
    ): PaymentBraintreeTransactionRequestLogTransfer {
        return $paymentBraintreeTransactionRequestLogTransfer->fromArray($paymentBraintreeTransactionRequestLogEntity->toArray(), true);
    }

    /**
     * @param PaymentBraintreeOrderItemTransfer $paymentBraintreeOrderItemTransfer
     * @param SpyPaymentBraintreeOrderItem $paymentBraintreeOrderItemEntity
     *
     * @return SpyPaymentBraintreeOrderItem
     */
    public function mapPaymentBraintreeOrderItemTransferToEntity(
        PaymentBraintreeOrderItemTransfer $paymentBraintreeOrderItemTransfer,
        SpyPaymentBraintreeOrderItem $paymentBraintreeOrderItemEntity
    ): SpyPaymentBraintreeOrderItem {
        $paymentBraintreeOrderItemEntity->fromArray($paymentBraintreeOrderItemTransfer->modifiedToArray());
        $paymentBraintreeOrderItemEntity->setNew($paymentBraintreeOrderItemTransfer->getIdPaymentBraintreeTransactionRequestLog() === null);

        return $paymentBraintreeOrderItemEntity;
    }

    /**
     * @param SpyPaymentBraintreeOrderItem $paymentBraintreeOrderItemEntity
     * @param PaymentBraintreeOrderItemTransfer $paymentBraintreeOrderItemTransfer
     *
     * @return PaymentBraintreeOrderItemTransfer
     */
    public function mapEntityToPaymentBraintreeOrderItemTransfer(
        SpyPaymentBraintreeOrderItem $paymentBraintreeOrderItemEntity,
        PaymentBraintreeOrderItemTransfer $paymentBraintreeOrderItemTransfer
    ): PaymentBraintreeOrderItemTransfer {
        return $paymentBraintreeOrderItemTransfer->fromArray($paymentBraintreeOrderItemEntity->toArray(), true);
    }
}
