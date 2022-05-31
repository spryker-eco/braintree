<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business\BraintreeFacade;

use Braintree\Transaction\CreditCardDetails;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\AbstractTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\PreCheckTransaction;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeBridge;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group BraintreeFacadePreCheckTest
 * Add your own group annotations below this line
 */
class BraintreeFacadePreCheckTest extends AbstractFacadeTest
{
    /**
     * @return void
     */
    public function testPreCheckPaymentWithSuccessfulResponse(): void
    {
        // Arrange
        $braintreeBusinessFactory = $this->getFactoryMock(['createPreCheckTransaction']);
        $braintreeBusinessFactory->expects($this->once())->method('createPreCheckTransaction')->willReturn(
            $this->getPreCheckTransactionMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($braintreeBusinessFactory);
        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->createQuoteTransfer($orderTransfer);

        // Act
        $braintreeTransactionResponseTransfer = $braintreeFacade->preCheckPayment($quoteTransfer);

        // Assert
        $this->assertTrue(
            $braintreeTransactionResponseTransfer->getIsSuccess(),
            'Facade should return success if the precheck payment transaction succeeded',
        );
        $this->assertNotEmpty(
            $braintreeTransactionResponseTransfer->getTransactionId(),
            'Transaction id should be set if the precheck payment transaction succeeded',
        );
    }

    /**
     * @return void
     */
    public function testPreCheckPaymentWithErrorResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createPreCheckTransaction']);
        $factoryMock->expects($this->once())->method('createPreCheckTransaction')->willReturn(
            $this->getPreCheckTransactionMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->createQuoteTransfer($orderTransfer);

        // Act
        $braintreeTransactionResponseTransfer = $braintreeFacade->preCheckPayment($quoteTransfer);

        // Assert
        $this->assertFalse(
            $braintreeTransactionResponseTransfer->getIsSuccess(),
            'Facade should return error if the precheck payment transaction failed',
        );
    }

    /**
     * @param bool $isSuccessful
     *
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\AbstractTransaction|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPreCheckTransactionMock(bool $isSuccessful = true): AbstractTransaction
    {
        /** @var \Spryker\Zed\Money\Business\MoneyFacadeInterface $moneyFacadeMock */
        $moneyFacadeMock = $this->getMoneyFacadeMock();
        $preCheckTransactionMock = $this
            ->getMockBuilder(PreCheckTransaction::class)
            ->setMethods(['preCheck', 'initializeBraintree'])
            ->setConstructorArgs(
                [new BraintreeConfig(), new BraintreeToMoneyFacadeBridge($moneyFacadeMock)],
            )
            ->getMock();

        if (!$isSuccessful) {
            $preCheckTransactionMock->expects($this->once())
                ->method('preCheck')
                ->willReturn($this->getErrorResponse());

            return $preCheckTransactionMock;
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
        $preCheckTransactionMock->expects($this->once())
            ->method('preCheck')
            ->willReturn($transactionResponse);

        return $preCheckTransactionMock;
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMoneyFacadeMock(): BraintreeToMoneyFacadeInterface
    {
        return $this->getMockBuilder(BraintreeToMoneyFacadeInterface::class)->getMock();
    }
}
