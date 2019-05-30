<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor;

use Generated\Shared\Transfer\PaymentBraintreeTransfer;
use Generated\Shared\Transfer\TransactionMetaTransfer;
use SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface;

class PaymentTransactionMetaVisitor implements TransactionMetaVisitorInterface
{
    protected const TRANSACTION_STATUS = 'settling';
    protected const TRANSACTION_CODE = 'capture';

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
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return void
     */
    public function visit(TransactionMetaTransfer $transactionMetaTransfer)
    {
        $paymentBraintreeTransfer = $this->findPaymentBraintreeTransfer($transactionMetaTransfer);

        if ($paymentBraintreeTransfer) {
            $transactionMetaTransfer->setIdPayment($paymentBraintreeTransfer->getIdPaymentBraintree());
            $transactionMetaTransfer->setTransactionIdentifier($paymentBraintreeTransfer->getTransactionId());

            if ($transactionMetaTransfer->getRefund() && $this->countRefundedUniqItems($transactionMetaTransfer->getRefund()->getItems()) === 1) {
                $transactionMetaTransfer->setTransactionIdentifier($this->getTransactionId($transactionMetaTransfer));
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransfer|null
     */
    protected function findPaymentBraintreeTransfer(TransactionMetaTransfer $transactionMetaTransfer): ?PaymentBraintreeTransfer
    {
        $idSalesOrderEntity = $transactionMetaTransfer->requireIdSalesOrder()->getIdSalesOrder();

        return $this->repository->findPaymentBraintreeBySalesOrderId($idSalesOrderEntity);
    }

    /**
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return string
     */
    protected function getTransactionId(TransactionMetaTransfer $transactionMetaTransfer): string
    {
        $idSalesOrderItem = $transactionMetaTransfer->getRefund()->getItems()[0]->getIdSalesOrderItem();

        $transactionId = $this->repository
            ->findSucceededPaymentBraintreeTransactionStatusLogQueryBySalesOrderIdAndTransactionCode(
                $transactionMetaTransfer->getIdSalesOrder(),
                static::TRANSACTION_CODE,
                static::TRANSACTION_STATUS
            )
            ->getTransactionId();

        $paymentBraintreeTransactionStatusLogTransferByOrderItem = $this->repository
            ->findPaymentBraintreeTransactionStatusLogQueryByOrderItem($idSalesOrderItem);

        if ($paymentBraintreeTransactionStatusLogTransferByOrderItem) {
            $transactionId = $this->repository
                ->findPaymentBraintreeTransactionStatusLogQueryByOrderItem($idSalesOrderItem)->getTransactionId();
        }

        return $transactionId;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return int
     */
    protected function countRefundedUniqItems(iterable $itemTransfers): int
    {
        $uniqItems = [];
        $count = 0;

        foreach ($itemTransfers as $itemTransfer) {
            if (!in_array($itemTransfer->getIdSalesOrderItem(), $uniqItems)) {
                $uniqItems[] = $itemTransfer->getIdSalesOrderItem();
                $count++;
            }
        }

        return $count;
    }
}
