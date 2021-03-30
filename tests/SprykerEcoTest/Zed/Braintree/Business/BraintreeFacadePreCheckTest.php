<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business;

use Braintree\Transaction\CreditCardDetails;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\PreCheckTransaction;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeBridge;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;

/**
 * Auto-generated group annotations
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
        $factoryMock = $this->getFactoryMock(['createPreCheckTransaction']);
        $factoryMock->expects($this->once())->method('createPreCheckTransaction')->willReturn(
            $this->getPreCheckTransactionMock()
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->getQuoteTransfer($orderTransfer);

        // Act
        $response = $braintreeFacade->preCheckPayment($quoteTransfer);

        // Assert
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testPreCheckPaymentWithErrorResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createPreCheckTransaction']);
        $factoryMock->expects($this->once())->method('createPreCheckTransaction')->willReturn(
            $this->getPreCheckTransactionMock(false)
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->getQuoteTransfer($orderTransfer);

        // Act
        $response = $braintreeFacade->preCheckPayment($quoteTransfer);

        // Assert
        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @param bool $success
     *
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\PreCheckTransaction|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPreCheckTransactionMock(bool $success = true): PreCheckTransaction
    {
        /** @var \Spryker\Zed\Money\Business\MoneyFacadeInterface $moneyFacadeMock */
        $moneyFacadeMock = $this->getMoneyFacadeMock();
        $preCheckTransactionMock = $this
            ->getMockBuilder(PreCheckTransaction::class)
            ->setMethods(['preCheck', 'initializeBraintree'])
            ->setConstructorArgs(
                [new BraintreeConfig(), new BraintreeToMoneyFacadeBridge($moneyFacadeMock)]
            )
            ->getMock();

        if (!$success) {
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
