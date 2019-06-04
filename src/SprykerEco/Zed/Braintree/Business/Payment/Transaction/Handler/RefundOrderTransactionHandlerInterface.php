<?php


namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;


use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;

interface RefundOrderTransactionHandlerInterface
{
    /**
     * @param array $salesOrderItems
     * @param SpySalesOrder $salesOrderEntity
     *
     * @return BraintreeTransactionResponseTransfer
     */
    public function refund(array $salesOrderItems, SpySalesOrder $salesOrderEntity): BraintreeTransactionResponseTransfer;
}
