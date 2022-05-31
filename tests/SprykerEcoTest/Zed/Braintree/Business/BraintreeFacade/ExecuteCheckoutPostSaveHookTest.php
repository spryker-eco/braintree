<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business\BraintreeFacade;

use Braintree\Transaction\CreditCardDetails;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\BraintreeFacade;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\PaymentTransaction;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeBridge;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;
use SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group ExecuteCheckoutPostSaveHookTest
 * Add your own group annotations below this line
 */
class ExecuteCheckoutPostSaveHookTest extends AbstractFacadeTest
{
    /**
     * @return void
     */
    public function testCheckoutPostSaveHookWithSuccessfulResponse(): void
    {
        // Arrange
        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->createQuoteTransfer($orderTransfer);
        $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer($orderTransfer);

        $braintreeFacade = $this->setupFacadeMock();

        // Act
        $checkoutResponseTransfer = $braintreeFacade->executeCheckoutPostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertTrue(
            $checkoutResponseTransfer->getIsSuccess(),
            'Checkout post save hook should return success if payment transaction succeeded',
        );
        $this->assertCount(
            0,
            $checkoutResponseTransfer->getErrors(),
            'Checkout response should not contain errors when payment transaction succeeded',
        );
        $this->assertNotEmpty(
            $quoteTransfer->getPayment()->getBraintree()->getTransactionId(),
            'quote.payment.braintree.transactionId should be set when payment transaction succeeded',
        );
        $this->assertNotEmpty(
            $quoteTransfer->getPayment()->getBraintreeTransactionResponse(),
            'quote.payment.braintreeTransactionResponse should be set when payment transaction succeeded',
        );
    }

    /**
     * @return void
     */
    public function testCheckoutPostSaveHookWithErrorResponse(): void
    {
        // Arrange
        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->createQuoteTransfer($orderTransfer);
        $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer($orderTransfer);

        $braintreeFacade = $this->setupFacadeMock(false);

        // Act
        $checkoutResponseTransfer = $braintreeFacade->executeCheckoutPostSaveHook($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertFalse(
            $checkoutResponseTransfer->getIsSuccess(),
            'Checkout post save hook should return error if payment transaction failed',
        );
        $this->assertCount(
            1,
            $checkoutResponseTransfer->getErrors(),
            'Checkout response should contain error message when payment transaction failed',
        );
        $this->assertNotEmpty(
            $quoteTransfer->getPayment()->getBraintreeTransactionResponse(),
            'quote.payment.braintreeTransactionResponse should be set when payment transaction failed',
        );
    }

    /**
     * @return void
     */
    public function testCheckoutPostSaveHookWithEmptyPaymentThrowsException(): void
    {
        // Arrange
        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->createQuoteTransferWithEmptyPayment($orderTransfer);
        $checkoutResponseTransfer = $this->tester->createCheckoutResponseTransfer($orderTransfer);

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->getBraintreeFacade()->executeCheckoutPostSaveHook($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @param bool $isSuccessfulTransaction
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\BraintreeFacade
     */
    protected function setupFacadeMock(bool $isSuccessfulTransaction = true): BraintreeFacade
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\BraintreeBusinessFactory $braintreeBusinessFactoryMock */
        $braintreeBusinessFactoryMock = $this->getFactoryMock(['createPaymentTransaction', 'getEntityManager']);
        $braintreeBusinessFactoryMock->expects($this->once())->method('createPaymentTransaction')->willReturn(
            $this->getPaymentTransactionMock($isSuccessfulTransaction),
        );
        $braintreeBusinessFactoryMock->method('getEntityManager')->willReturn(
            $this->getBraintreeEntityManagerMock(),
        );

        return $this->getBraintreeFacade($braintreeBusinessFactoryMock);
    }

    /**
     * @param bool $isSuccessful
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\PaymentTransaction
     */
    protected function getPaymentTransactionMock(bool $isSuccessful = true): PaymentTransaction
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

        if (!$isSuccessful) {
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface
     */
    protected function getBraintreeEntityManagerMock(): BraintreeEntityManagerInterface
    {
        return $this
            ->getMockBuilder(BraintreeEntityManagerInterface::class)
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
