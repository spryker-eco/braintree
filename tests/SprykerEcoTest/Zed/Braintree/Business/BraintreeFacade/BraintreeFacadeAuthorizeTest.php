<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business\BraintreeFacade;

use Braintree\Exception\NotFound;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\AuthorizeTransaction;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group BraintreeFacadeAuthorizeTest
 * Add your own group annotations below this line
 */
class BraintreeFacadeAuthorizeTest extends AbstractFacadeTest
{
    /**
     * @return void
     */
    public function testAuthorizePaymentWithSuccessResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createAuthorizeTransaction']);
        $factoryMock->expects($this->once())->method('createAuthorizeTransaction')->willReturn(
            $this->getAuthorizeTransactionMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $transactionMetaTransfer = $this->getTransactionMetaTransfer();

        // Act
        $response = $braintreeFacade->authorizePayment($transactionMetaTransfer);
        $isSuccess = $response->getIsSuccess();

        // Assert
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testAuthorizePaymentWithErrorResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createAuthorizeTransaction']);
        $factoryMock->expects($this->once())->method('createAuthorizeTransaction')->willReturn(
            $this->getAuthorizeTransactionMock(true),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $transactionMetaTransfer = $this->getTransactionMetaTransfer();

        // Act
        $response = $braintreeFacade->authorizePayment($transactionMetaTransfer);
        $isSuccess = $response->getIsSuccess();
        // Assert
        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @param bool $throwsException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\AuthorizeTransaction
     */
    protected function getAuthorizeTransactionMock($throwsException = false): AuthorizeTransaction
    {
        $authorizeTransactionMock = $this
            ->getMockBuilder(AuthorizeTransaction::class)
            ->setMethods(['findTransaction', 'initializeBraintree'])
            ->setConstructorArgs(
                [new BraintreeConfig()],
            )
            ->getMock();

        if ($throwsException) {
            $authorizeTransactionMock->method('findTransaction')->willThrowException(new NotFound());

            return $authorizeTransactionMock;
        }

        $transaction = $this->tester->getSuccessfulTransaction([
            'amount' => $this->createOrderTransfer()->getTotals()->getGrandTotal() / 100,
        ]);

        $authorizeTransactionMock->expects($this->once())
            ->method('findTransaction')
            ->willReturn($transaction);

        return $authorizeTransactionMock;
    }
}
