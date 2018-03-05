<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor;

use Generated\Shared\Transfer\TransactionMetaTransfer;
use SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface;

class PaymentTransactionMetaVisitor implements TransactionMetaVisitorInterface
{
    /**
     * @var \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface $queryContainer
     */
    public function __construct(BraintreeQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return void
     */
    public function visit(TransactionMetaTransfer $transactionMetaTransfer)
    {
        $paymentEntity = $this->getPaymentEntity($transactionMetaTransfer);

        $transactionMetaTransfer->setIdPayment($paymentEntity->getIdPaymentBraintree());
        $transactionMetaTransfer->setTransactionIdentifier($paymentEntity->getTransactionId());
    }

    /**
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintree
     */
    protected function getPaymentEntity(TransactionMetaTransfer $transactionMetaTransfer)
    {
        $idSalesOrderEntity = $transactionMetaTransfer->requireIdSalesOrder()->getIdSalesOrder();

        return $this->queryContainer->queryPaymentBySalesOrderId($idSalesOrderEntity)->findOne();
    }
}
