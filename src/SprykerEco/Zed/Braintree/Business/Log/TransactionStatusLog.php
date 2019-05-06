<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Log;

use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;
use SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface;

class TransactionStatusLog implements TransactionStatusLogInterface
{
    /**
     * @var \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface
     */
    protected $repository;

    /**
     * @param \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface $repository
     */
    public function __construct(BraintreeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isAuthorizationApproved(OrderTransfer $orderTransfer)
    {
        return $this->hasTransactionStatusLog(
            $orderTransfer,
            ApiConstants::TRANSACTION_CODE_AUTHORIZE,
            ApiConstants::STATUS_CODE_AUTHORIZE,
            ApiConstants::STATUS_REASON_CODE_SUCCESS
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isReversalApproved(OrderTransfer $orderTransfer)
    {
        return $this->hasTransactionStatusLog(
            $orderTransfer,
            ApiConstants::TRANSACTION_CODE_REVERSAL,
            ApiConstants::STATUS_CODE_REVERSAL,
            ApiConstants::STATUS_REASON_CODE_SUCCESS
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCaptureApproved(OrderTransfer $orderTransfer)
    {
        if ($this->hasTransactionStatusLog(
            $orderTransfer,
            ApiConstants::TRANSACTION_CODE_CAPTURE,
            ApiConstants::STATUS_CODE_CAPTURE,
            ApiConstants::STATUS_REASON_CODE_SUCCESS
        )) {
            return true;
        }

        return $this->hasTransactionStatusLog(
            $orderTransfer,
            ApiConstants::TRANSACTION_CODE_CAPTURE,
            ApiConstants::STATUS_CODE_CAPTURE_SUBMITTED,
            ApiConstants::STATUS_REASON_CODE_SUCCESS
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isRefundApproved(OrderTransfer $orderTransfer)
    {
        return $this->hasTransactionStatusLog(
            $orderTransfer,
            ApiConstants::TRANSACTION_CODE_REFUND,
            [ApiConstants::STATUS_CODE_REVERSAL, ApiConstants::STATUS_CODE_REFUND],
            ApiConstants::STATUS_REASON_CODE_SUCCESS
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param string $transactionCode
     * @param string|array $statusCode
     * @param string $expectedStatusReasonCode
     *
     * @return bool
     */
    protected function hasTransactionStatusLog(OrderTransfer $orderTransfer, $transactionCode, $statusCode, $expectedStatusReasonCode)
    {
        $idSalesOrder = $orderTransfer->getIdSalesOrder();

        $logEntity = $this->repository
            ->findPaymentBraintreeTransactionStatusLogQueryBySalesOrderIdAndTransactionCodeLatestFirst(
                $idSalesOrder,
                $transactionCode,
                $statusCode
            );

        if (!$logEntity) {
            return false;
        }

        return $logEntity->getIsSuccess() === (bool)$expectedStatusReasonCode;
    }
}
