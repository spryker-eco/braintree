<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business;

use Braintree\Result\Successful;
use Braintree\Transaction;
use Braintree\Transaction\CreditCardDetails;
use Braintree\Transaction\StatusDetails;
use DateTime;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Order\Saver;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\PaymentTransaction;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeBridge;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;
use SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManager;

/**
 * Auto-generated group annotations
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group BraintreeFacadeCheckoutPostSaveHookTest
 * Add your own group annotations below this line
 */
class BraintreeFacadeCheckoutPostSaveHookTest extends AbstractFacadeTest
{
    /**
     * @return void
     */
    public function testCheckoutPostSaveHookWithSuccessfulResponse()
    {
        $response = $this->executeCheckoutPostSaveHook();
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCheckoutPostSaveHookWithErrorResponse()
    {
        $response = $this->executeCheckoutPostSaveHook(false);
        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @param bool $isSuccess
     *
     * @return @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function executeCheckoutPostSaveHook(bool $isSuccess = true): CheckoutResponseTransfer
    {
        $factoryMock = $this->getFactoryMock(['createPaymentTransaction', 'createOrderSaver']);
        $factoryMock->expects($this->once())->method('createPaymentTransaction')->willReturn(
            $this->getPaymentTransactionMock($isSuccess)
        );
        $factoryMock->expects($this->once())->method('createOrderSaver')->willReturn(
            $this->getOrderSaverMock()
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->getQuoteTransfer($orderTransfer);
        $checkoutResponseTransfer = $this->getCheckoutResponseTransfer($orderTransfer);

        return $braintreeFacade->checkoutPostSaveHook($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getPaymentTransactionMock(bool $success = true)
    {
        $moneyFacadeMock = $this->getMoneyFacadeMock();
        $paymentTransactionMock = $this
            ->getMockBuilder(PaymentTransaction::class)
            ->setMethods(['doTransaction', 'initializeBraintree'])
            ->setConstructorArgs(
                [new BraintreeConfig(), new BraintreeToMoneyFacadeBridge($moneyFacadeMock)]
            )
            ->getMock();

        $doRequestResponse = $success ? $this->getSuccessResponse() : $this->getErrorResponse();
        $paymentTransactionMock->expects($this->once())->method('doTransaction')->willReturn($doRequestResponse);

        return $paymentTransactionMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getOrderSaverMock()
    {
        return $this
            ->getMockBuilder(Saver::class)
            ->setConstructorArgs([new BraintreeEntityManager()])
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMoneyFacadeMock()
    {
        return $this
            ->getMockBuilder(BraintreeToMoneyFacadeInterface::class)
            ->getMock();
    }

    /**
     * @return \Braintree\Result\Successful
     */
    protected function getSuccessResponse()
    {
        $transaction = Transaction::factory([
            'id' => 1,
            'paymentInstrumentType' => 'paypal_account',
            'processorSettlementResponseCode' => null,
            'processorResponseCode' => '1000',
            'processorResponseText' => 'Approved',
            'createdAt' => new DateTime(),
            'status' => 'authorized',
            'type' => 'sale',
            'amount' => $this->createOrderTransfer()->getTotals()->getGrandTotal() / 100,
            'merchantAccountId' => 'abc',
            'statusHistory' => new StatusDetails([
                'timestamp' => new DateTime(),
                'status' => 'authorized',
            ]),
            'creditCardDetails' => new CreditCardDetails([
                'expirationMonth' => null,
                'expirationYear' => null,
                'bin' => null,
                'last4' => null,
                'cardType' => null,
            ]),
        ]);

        return new Successful($transaction);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function getCheckoutResponseTransfer(OrderTransfer $orderTransfer)
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
    protected function createSaveOrderTransfer(OrderTransfer $orderTransfer)
    {
        $saveOrderTransfer = new SaveOrderTransfer();
        $saveOrderTransfer->setIdSalesOrder($orderTransfer->getIdSalesOrder());

        return $saveOrderTransfer;
    }
}
