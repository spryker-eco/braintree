<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree;

use Braintree\Result\Successful;
use Braintree\Transaction;
use Braintree\Transaction\StatusDetails;
use Codeception\Actor;
use DateTime;
use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class BraintreeBusinessTester extends Actor
{
    use _generated\BraintreeBusinessTesterActions;

    /**
     * @param array $params
     *
     * @return \Braintree\Transaction
     */
   public function getSuccessfulTransaction($params = []): Transaction
   {
       $defaultParams = [
           'id' => 123,
           'processorResponseCode' => '1000',
           'processorResponseText' => 'Approved',
           'createdAt' => new DateTime(),
           'status' => 'authorized',
           'type' => 'sale',
           'amount' => 0,
           'merchantAccountId' => 'abc',
           'statusHistory' => new StatusDetails([
               'timestamp' => new DateTime(),
               'status' => 'authorized',
           ]),
       ];

       return  Transaction::factory(array_merge($defaultParams, $params));
   }

    /**
     * @param array $params
     *
     * @return \Braintree\Result\Successful
     */
   public function getSuccessfulTransactionResponse($params = []): Successful
   {
       return new Successful($this->getSuccessfulTransaction($params));
   }

    /**
     * @param bool $isSuccess
     *
     * @return BraintreeTransactionResponseTransfer
     */
    public function getBraintreeTransactionResponseTransfer(bool $isSuccess): BraintreeTransactionResponseTransfer
    {
        return (new BraintreeTransactionResponseTransfer())
            ->setIsSuccess($isSuccess);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function getCheckoutResponseTransfer(OrderTransfer $orderTransfer): CheckoutResponseTransfer
    {
        $saveOrderTransfer = $this->createSaveOrderTransfer($orderTransfer);

        $checkoutTransfer = new CheckoutResponseTransfer();
        $checkoutTransfer->setSaveOrder($saveOrderTransfer);

        return $checkoutTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\SaveOrderTransfer
     */
    public function createSaveOrderTransfer(OrderTransfer $orderTransfer): SaveOrderTransfer
    {
        $saveOrderTransfer = new SaveOrderTransfer();
        $saveOrderTransfer->setIdSalesOrder($orderTransfer->getIdSalesOrder());

        return $saveOrderTransfer;
    }
}
