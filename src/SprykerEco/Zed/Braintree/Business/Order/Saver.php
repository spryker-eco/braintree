<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Order;

use Generated\Shared\Transfer\BraintreePaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintree;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeOrderItem;
use SprykerEco\Shared\Braintree\BraintreeConfig;

class Saver implements SaverInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        if ($quoteTransfer->getPayment()->getPaymentProvider() === BraintreeConfig::PROVIDER_NAME
            && $quoteTransfer->getPayment()->getBraintreeTransactionResponse()->getIsSuccess()
        ) {
            $paymentEntity = $this->savePaymentForOrder(
                $quoteTransfer->getPayment()->getBraintree(),
                $saveOrderTransfer->getIdSalesOrder()
            );

            $this->savePaymentForOrderItems(
                $saveOrderTransfer->getOrderItems(),
                $paymentEntity->getIdPaymentBraintree()
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\BraintreePaymentTransfer $paymentTransfer
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintree
     */
    protected function savePaymentForOrder(BraintreePaymentTransfer $paymentTransfer, $idSalesOrder)
    {
        $paymentEntity = new SpyPaymentBraintree();
        $addressTransfer = $paymentTransfer->getBillingAddress();

        $formattedStreet = trim(sprintf(
            '%s %s %s',
            $addressTransfer->getAddress1(),
            $addressTransfer->getAddress2(),
            $addressTransfer->getAddress3()
        ));

        $paymentEntity->fromArray($addressTransfer->toArray());
        $paymentEntity->fromArray($paymentTransfer->toArray());

        $paymentEntity
            ->setStreet($formattedStreet)
            ->setCountryIso2Code($addressTransfer->getIso2Code())
            ->setFkSalesOrder($idSalesOrder);
        $paymentEntity->save();

        return $paymentEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $orderItemTransfers
     * @param int $idPayment
     *
     * @return void
     */
    protected function savePaymentForOrderItems($orderItemTransfers, $idPayment)
    {
        foreach ($orderItemTransfers as $orderItemTransfer) {
            $paymentOrderItemEntity = new SpyPaymentBraintreeOrderItem();
            $paymentOrderItemEntity
                ->setFkPaymentBraintree($idPayment)
                ->setFkSalesOrderItem($orderItemTransfer->getIdSalesOrderItem());
            $paymentOrderItemEntity->save();
        }
    }
}
