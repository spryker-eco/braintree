<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business;

use Braintree\Transaction\StatusDetails;
use DateTime;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\RevertTransaction;

/**
 * Auto-generated group annotations
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group BraintreeFacadeRevertTest
 * Add your own group annotations below this line
 */
class BraintreeFacadeRevertTest extends AbstractFacadeTest
{
    /**
     * @return void
     */
    public function testRevertPaymentWithSuccessResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createRevertTransaction']);
        $factoryMock->expects($this->once())->method('createRevertTransaction')->willReturn(
            $this->getRevertTransactionMock()
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $transactionMetaTransfer = $this->getTransactionMetaTransfer();

        // Act
        $response = $braintreeFacade->revertPayment($transactionMetaTransfer);

        // Assert
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testRevertPaymentWithErrorResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createRevertTransaction']);
        $factoryMock->expects($this->once())->method('createRevertTransaction')->willReturn(
            $this->getRevertTransactionMock(false)
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $transactionMetaTransfer = $this->getTransactionMetaTransfer();

        // Act
        $response = $braintreeFacade->revertPayment($transactionMetaTransfer);

        // Assert
        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\RevertTransaction
     */
    protected function getRevertTransactionMock($success = true): RevertTransaction
    {
        $revertTransactionMock = $this
            ->getMockBuilder(RevertTransaction::class)
            ->setMethods(['revert', 'initializeBraintree'])
            ->setConstructorArgs([new BraintreeConfig()])
            ->getMock();

        if (!$success) {
            $revertTransactionMock->method('revert')->willReturn($this->getErrorResponse());

            return $revertTransactionMock;
        }

        $transactionResponse = $this->tester->getSuccessfulTransactionResponse([
            'status' => 'revert',
            'amount' => $this->createOrderTransfer()->getTotals()->getGrandTotal() / 100,
            'statusHistory' => new StatusDetails([
                'timestamp' => new DateTime(),
                'status' => 'voided',
            ]),
        ]);

        $revertTransactionMock->method('revert')->willReturn($transactionResponse);

        return $revertTransactionMock;
    }
}
