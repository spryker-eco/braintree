<?php


namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;

use Orm\Zed\Sales\Persistence\SpySalesOrder;

interface RefundItemsTransactionHandlerInterface
{
    /**
     * @param array $salesOrderItems
     * @param SpySalesOrder $salesOrderEntity
     *
     * @return void
     */
    public function refund(array $salesOrderItems, SpySalesOrder $salesOrderEntity): void;
}
