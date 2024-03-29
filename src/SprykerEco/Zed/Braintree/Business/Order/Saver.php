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
use SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface;

class Saver implements SaverInterface
{
    /**
     * @var \SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface
     */
    protected $braintreeEntityManager;

    /**
     * @param \SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface $braintreeEntityManager
     */
    public function __construct(BraintreeEntityManagerInterface $braintreeEntityManager)
    {
        $this->braintreeEntityManager = $braintreeEntityManager;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     * @param bool $saveOnlyIfTransactionSuccessful
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer, bool $saveOnlyIfTransactionSuccessful = true): void
    {
        if ($quoteTransfer->getPayment()->getPaymentProvider() !== BraintreeConfig::PROVIDER_NAME) {
            return;
        }

        if ($saveOnlyIfTransactionSuccessful && !$quoteTransfer->getPayment()->getBraintreeTransactionResponse()->getIsSuccess()) {
            return;
        }

        $paymentEntity = $this->savePaymentForOrder(
            $quoteTransfer->getPayment()->getBraintree(),
            (int)$saveOrderTransfer->getIdSalesOrder(),
        );

        $this->savePaymentForOrderItems(
            $saveOrderTransfer->getOrderItems(),
            $paymentEntity->getIdPaymentBraintree(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function updateOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        $paymentTransfer = $quoteTransfer->getPaymentOrFail();
        if ($paymentTransfer->getPaymentProvider() !== BraintreeConfig::PROVIDER_NAME) {
            return;
        }

        $braintreePaymentTransfer = $paymentTransfer->getBraintreeOrFail();
        $braintreePaymentTransfer->setFkSalesOrder($saveOrderTransfer->getIdSalesOrderOrFail());

        $this->braintreeEntityManager->updatePaymentBraintree($braintreePaymentTransfer);
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
            $addressTransfer->getAddress3(),
        ));

        $paymentEntity->fromArray($addressTransfer->toArray());
        $paymentEntity->fromArray($paymentTransfer->toArray());

        $paymentEntity
            ->setStreet($formattedStreet)
            ->setCountryIso2Code($addressTransfer->getIso2CodeOrFail())
            ->setFkSalesOrder($idSalesOrder);
        $paymentEntity->save();

        return $paymentEntity;
    }

    /**
     * @param iterable<\Generated\Shared\Transfer\ItemTransfer> $orderItemTransfers
     * @param int $idPayment
     *
     * @return void
     */
    protected function savePaymentForOrderItems(iterable $orderItemTransfers, $idPayment)
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
