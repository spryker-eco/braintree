<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence;

interface BraintreeEntityManagerInterface
{
    /**
     * @param int $idPaymentBraintree
     * @param bool $isShipmentPaid
     *
     * @return void
     */
    public function updateIsShipmentPaidValue(int $idPaymentBraintree, bool $isShipmentPaid): void;

    /**
     * @param int $idPaymentBraintree
     * @param string $transactionId
     * @param bool $isShipmentOperation
     *
     * @return void
     */
    public function updateIsShipmentOperationValue(int $idPaymentBraintree, string $transactionId, bool $isShipmentOperation): void;

    /**
     * @param int $idPaymentBraintree
     * @param iterable<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     * @param string $transactionId
     *
     * @return void
     */
    public function addOrderItemsToTransactionLog(int $idPaymentBraintree, iterable $itemTransfers, string $transactionId): void;
}
