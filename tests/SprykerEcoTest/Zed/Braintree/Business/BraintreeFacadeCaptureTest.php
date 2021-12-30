<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business;

use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureItemsTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureOrderTransaction;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group BraintreeFacadeCaptureTest
 * Add your own group annotations below this line
 */
class BraintreeFacadeCaptureTest extends AbstractFacadeTest
{
    /**
     * @return void
     */
    public function testCapturePaymentWithSuccessResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createCaptureOrderTransaction']);
        $factoryMock->method('createCaptureOrderTransaction')->willReturn(
            $this->getCaptureOrderTransactionMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $response = $braintreeFacade->capturePayment($this->getTransactionMetaTransfer());

        // Assert
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCapturePaymentWithFailureResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createCaptureOrderTransaction']);
        $factoryMock->method('createCaptureOrderTransaction')->willReturn(
            $this->getCaptureOrderTransactionMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $response = $braintreeFacade->capturePayment($this->getTransactionMetaTransfer());

        // Assert
        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCaptureOrderPaymentWithSuccessResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createCaptureOrderTransaction']);
        $factoryMock->method('createCaptureOrderTransaction')->willReturn(
            $this->getCaptureOrderTransactionMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $response = $braintreeFacade->captureOrderPayment($this->getTransactionMetaTransfer());

        // Assert
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCaptureOrderPaymentWithFailureResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createCaptureOrderTransaction']);
        $factoryMock->method('createCaptureOrderTransaction')->willReturn(
            $this->getCaptureOrderTransactionMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $response = $braintreeFacade->captureOrderPayment($this->getTransactionMetaTransfer());

        // Assert
        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCaptureItemsPaymentWithSuccessResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createCaptureItemsTransaction']);
        $factoryMock->method('createCaptureItemsTransaction')->willReturn(
            $this->getCaptureItemsTransactionMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $response = $braintreeFacade->captureItemsPayment($this->getTransactionMetaTransfer());

        // Assert
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCaptureItemsPaymentWithFailureResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createCaptureItemsTransaction']);
        $factoryMock->method('createCaptureItemsTransaction')->willReturn(
            $this->getCaptureItemsTransactionMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $response = $braintreeFacade->captureItemsPayment($this->getTransactionMetaTransfer());

        // Assert
        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureOrderTransaction
     */
    protected function getCaptureOrderTransactionMock(bool $success = true): CaptureOrderTransaction
    {
        $captureTransactionMock = $this
            ->getMockBuilder(CaptureOrderTransaction::class)
            ->setMethods(['capture', 'initializeBraintree'])
            ->setConstructorArgs(
                [new BraintreeConfig()],
            )
            ->getMock();

        if (!$success) {
            $captureTransactionMock->method('capture')->willReturn($this->getErrorResponse());

            return $captureTransactionMock;
        }

        $transactionResponse = $this->tester->getSuccessfulTransactionResponse([
            'status' => 'settling',
            'amount' => $this->createOrderTransfer()->getTotals()->getGrandTotal() / 100,
        ]);
        $captureTransactionMock->method('capture')->willReturn($transactionResponse);

        return $captureTransactionMock;
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureItemsTransaction
     */
    protected function getCaptureItemsTransactionMock(bool $success = true): CaptureItemsTransaction
    {
        $captureTransactionMock = $this
            ->getMockBuilder(CaptureItemsTransaction::class)
            ->setMethods(['capture', 'initializeBraintree', 'afterTransaction'])
            ->disableOriginalConstructor()
            ->setConstructorArgs(
                [new BraintreeConfig()],
            )
            ->getMock();

        if (!$success) {
            $captureTransactionMock->method('capture')->willReturn($this->getErrorResponse());

            $captureTransactionMock->method('afterTransaction')->willReturn(
                $this->tester->getBraintreeTransactionResponseTransfer(false),
            );

            return $captureTransactionMock;
        }

        $transactionResponse = $this->tester->getSuccessfulTransactionResponse([
            'status' => 'settling',
            'amount' => $this->createOrderTransfer()->getTotals()->getGrandTotal() / 100,
        ]);
        $captureTransactionMock->method('capture')->willReturn($transactionResponse);
        $captureTransactionMock->method('afterTransaction')->willReturn(
            $this->tester->getBraintreeTransactionResponseTransfer(true),
        );

        return $captureTransactionMock;
    }
}
