<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business\BraintreeFacade;

use Braintree\Transaction\CreditCardDetails;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Order\Saver;
use SprykerEco\Zed\Braintree\Business\Order\SaverInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\PaymentTransaction;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeBridge;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;
use SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManager;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group BraintreeFacadeCheckoutPostSaveHookTest
 * Add your own group annotations below this line
 */
class ExecuteCheckoutPostSaveHookTest extends AbstractFacadeTest
{
    /**
     * @return void
     */
    public function testCheckoutPostSaveHookWithSuccessfulResponse(): void
    {
        // Act
        $checkoutResponseTransfer = $this->executeCheckoutPostSaveHook();

        // Assert
        $this->assertTrue($checkoutResponseTransfer->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCheckoutPostSaveHookWithErrorResponse(): void
    {
        // Act
        $response = $this->executeCheckoutPostSaveHook(false);

        // Assert
        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCheckoutPostSaveHookWithEmptyPaymentExpectException(): void
    {
        // Assert
        $this->expectException(RequiredTransferPropertyException::class);

        // Act
        $this->executeCheckoutPostSaveHookWithEmptyPayment(true);
    }

    /**
     * @param bool $isSuccess
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function executeCheckoutPostSaveHook(bool $isSuccess = true): CheckoutResponseTransfer
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\BraintreeBusinessFactory $braintreeBusinessFactoryMock */
        $braintreeBusinessFactoryMock = $this->getFactoryMock(['createPaymentTransaction', 'createOrderSaver']);
        $braintreeBusinessFactoryMock->expects($this->once())->method('createPaymentTransaction')->willReturn(
            $this->getPaymentTransactionMock($isSuccess),
        );
        $braintreeBusinessFactoryMock->expects($this->once())->method('createOrderSaver')->willReturn(
            $this->getOrderSaverMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($braintreeBusinessFactoryMock);

        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->createQuoteTransfer($orderTransfer);
        $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer($orderTransfer);

        return $braintreeFacade->executeCheckoutPostSaveHook($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    protected function executeCheckoutPostSaveHookWithEmptyPayment(): CheckoutResponseTransfer
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\BraintreeBusinessFactory $braintreeBusinessFactoryMock */
        $braintreeBusinessFactoryMock = $this->getFactoryMock(['createPaymentTransaction', 'createOrderSaver']);
        $braintreeBusinessFactoryMock->expects($this->once())->method('createPaymentTransaction')->willReturn(
            $this->getPaymentTransactionMock(false),
        );
        $braintreeBusinessFactoryMock->expects($this->once())->method('createOrderSaver')->willReturn(
            $this->getOrderSaverMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($braintreeBusinessFactoryMock);

        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->createQuoteTransferWithEmptyPayment($orderTransfer);
        $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer($orderTransfer);

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
                [new BraintreeConfig(), new BraintreeToMoneyFacadeBridge($moneyFacadeMock)],
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\Order\SaverInterface
     */
    protected function getOrderSaverMock(): SaverInterface
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
