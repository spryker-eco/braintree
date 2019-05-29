<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence;

use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreePersistenceFactory getFactory()
 */
class BraintreeEntityManager extends AbstractEntityManager implements BraintreeEntityManagerInterface
{
    /**
     * @param int $idPaymentBraintree
     * @param bool $isShipmentPaid
     *
     * @return void
     */
    public function updateIsShipmentPaidValue(int $idPaymentBraintree, bool $isShipmentPaid): void
    {
        $paymentBraintreeEntity = $this->getFactory()->createPaymentBraintreeQuery()->findOneByIdPaymentBraintree($idPaymentBraintree);

        if ($paymentBraintreeEntity) {
            $paymentBraintreeEntity->setIsShipmentPaid($isShipmentPaid);
            $paymentBraintreeEntity->save();
        }
    }
}
