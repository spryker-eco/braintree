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
        $paymentBraintreeTransfer = $this->findPaymentEntity($transactionMetaTransfer);

        if ($paymentBraintreeTransfer) {
            $transactionMetaTransfer->setIdPayment($paymentBraintreeTransfer->getIdPaymentBraintree());
            $transactionMetaTransfer->setTransactionIdentifier($paymentBraintreeTransfer->getTransactionId());
        }
    }

    /**
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransfer|null
     */
    protected function findPaymentEntity(TransactionMetaTransfer $transactionMetaTransfer): ?PaymentBraintreeTransfer
    {
        $idSalesOrderEntity = $transactionMetaTransfer->requireIdSalesOrder()->getIdSalesOrder();

        return $this->repository->findPaymentBraintreeBySalesOrderId($idSalesOrderEntity);
    }
}
