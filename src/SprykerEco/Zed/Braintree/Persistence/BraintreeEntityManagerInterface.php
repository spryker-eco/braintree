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
     * @param int $idPaymentBrainreeOrderItem
     *
     * @return void
     */
    public function addOrderItemToSuccessLog(int $idPaymentBraintree, int $idPaymentBrainreeOrderItem): void;
}
