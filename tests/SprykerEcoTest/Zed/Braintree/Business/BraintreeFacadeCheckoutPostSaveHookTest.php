<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business;

use Braintree\Transaction\CreditCardDetails;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
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
    public function testCheckoutPostSaveHookWithSuccessfulResponse(): void
    {
        // Arrange
        $response = $this->executeCheckoutPostSaveHook();

        // Act
        $result = $response->getIsSuccess();

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testCheckoutPostSaveHookWithErrorResponse(): void
    {
        // Arrange
        $response = $this->executeCheckoutPostSaveHook(false);

        // Act
        $result = $response->getIsSuccess();

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @param bool $isSuccess
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function executeCheckoutPostSaveHook(bool $isSuccess = true): CheckoutResponseTransfer
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\BraintreeBusinessFactory $factoryMock */
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
        $checkoutResponseTransfer = $this->tester->getCheckoutResponseTransfer($orderTransfer);

        return $braintreeFacade->executeCheckoutPostSaveHook($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\PaymentTransaction
     */
    protected function getPaymentTransactionMock(bool $success = true): PaymentTransaction
    {
        /** @var \Spryker\Zed\Money\Business\MoneyFacadeInterface $moneyFacadeMock */
        $moneyFacadeMock = $this->getMoneyFacadeMock();
        $paymentTransactionMock = $this
            ->getMockBuilder(PaymentTransaction::class)
            ->setMethods(['doTransaction', 'initializeBraintree'])
            ->setConstructorArgs(
                [new BraintreeConfig(), new BraintreeToMoneyFacadeBridge($moneyFacadeMock)]
            )
            ->getMock();

        if (!$success) {
            $paymentTransactionMock->expects($this->once())
                ->method('doTransaction')
                ->willReturn($this->getErrorResponse());

            return $paymentTransactionMock;
        }

        $transactionResponse = $this->tester->getSuccessfulTransactionResponse([
            'paymentInstrumentType' => 'paypal_account',
            'processorSettlementResponseCode' => null,
            'amount' => $this->createOrderTransfer()->getTotals()->getGrandTotal() / 100,
            'creditCardDetails' => new CreditCardDetails([
                'expirationMonth' => null,
                'expirationYear' => null,
                'bin' => null,
                'last4' => null,
                'cardType' => null,
            ]),
        ]);

        $paymentTransactionMock->expects($this->once())
            ->method('doTransaction')
            ->willReturn($transactionResponse);

        return $paymentTransactionMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\Order\Saver
     */
    protected function getOrderSaverMock(): Saver
    {
        return $this
            ->getMockBuilder(Saver::class)
            ->setConstructorArgs([new BraintreeEntityManager()])
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface
     */
    protected function getMoneyFacadeMock(): BraintreeToMoneyFacadeInterface
    {
        return $this
            ->getMockBuilder(BraintreeToMoneyFacadeInterface::class)
            ->getMock();
    }
}
