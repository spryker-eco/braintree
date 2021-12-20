<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business;

use Braintree\Result\Successful;
use Braintree\Transaction;
use Braintree\Transaction\StatusDetails;
use DateTime;
use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
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
        $factoryMock = $this->getFactoryMock(['createCaptureOrderTransaction']);
        $factoryMock->method('createCaptureOrderTransaction')->willReturn(
            $this->getCaptureOrderTransactionMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->capturePayment($this->getTransactionMetaTransfer());

        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCapturePaymentWithFailureResponse(): void
    {
        $factoryMock = $this->getFactoryMock(['createCaptureOrderTransaction']);
        $factoryMock->method('createCaptureOrderTransaction')->willReturn(
            $this->getCaptureOrderTransactionMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->capturePayment($this->getTransactionMetaTransfer());

        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCaptureOrderPaymentWithSuccessResponse(): void
    {
        $factoryMock = $this->getFactoryMock(['createCaptureOrderTransaction']);
        $factoryMock->method('createCaptureOrderTransaction')->willReturn(
            $this->getCaptureOrderTransactionMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->captureOrderPayment($this->getTransactionMetaTransfer());

        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCaptureOrderPaymentWithFailureResponse(): void
    {
        $factoryMock = $this->getFactoryMock(['createCaptureOrderTransaction']);
        $factoryMock->method('createCaptureOrderTransaction')->willReturn(
            $this->getCaptureOrderTransactionMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->captureOrderPayment($this->getTransactionMetaTransfer());

        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCaptureItemsPaymentWithSuccessResponse(): void
    {
        $factoryMock = $this->getFactoryMock(['createCaptureItemsTransaction']);
        $factoryMock->method('createCaptureItemsTransaction')->willReturn(
            $this->getCaptureItemsTransactionMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->captureItemsPayment($this->getTransactionMetaTransfer());

        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCaptureItemsPaymentWithFailureResponse(): void
    {
        $factoryMock = $this->getFactoryMock(['createCaptureItemsTransaction']);
        $factoryMock->method('createCaptureItemsTransaction')->willReturn(
            $this->getCaptureItemsTransactionMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->captureItemsPayment($this->getTransactionMetaTransfer());

        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureOrderTransaction
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

        if ($success) {
            $captureTransactionMock->method('capture')->willReturn(
                $this->getSuccessResponse(),
            );
        } else {
            $captureTransactionMock->method('capture')->willReturn(
                $this->getErrorResponse(),
            );
        }

        return $captureTransactionMock;
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureItemsTransaction
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

        if ($success) {
            $captureTransactionMock->method('capture')->willReturn(
                $this->getSuccessResponse(),
            );
            $captureTransactionMock->method('afterTransaction')->willReturn(
                $this->getTransactionResponseSuccessTransfer(),
            );
        } else {
            $captureTransactionMock->method('capture')->willReturn(
                $this->getErrorResponse(),
            );
            $captureTransactionMock->method('afterTransaction')->willReturn(
                $this->getTransactionResponseFailureTransfer(),
            );
        }

        return $captureTransactionMock;
    }

    /**
     * @return \Braintree\Result\Successful
     */
    protected function getSuccessResponse(): Successful
    {
        $transaction = Transaction::factory([
            'id' => 123,
            'processorResponseCode' => 1000,
            'processorResponseText' => 'Approved',
            'createdAt' => new DateTime(),
            'status' => 'settling',
            'type' => 'sale',
            'amount' => $this->createOrderTransfer()->getTotals()->getGrandTotal() / 100,
            'merchantAccountId' => 'abc',
            'statusHistory' => new StatusDetails([
                'timestamp' => new DateTime(),
                'status' => 'settling',
            ]),
        ]);

        $response = new Successful([$transaction]);

        return $response;
    }

    /**
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    protected function getTransactionResponseSuccessTransfer(): BraintreeTransactionResponseTransfer
    {
        return (new BraintreeTransactionResponseTransfer())
            ->setIsSuccess(true);
    }

    /**
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    protected function getTransactionResponseFailureTransfer(): BraintreeTransactionResponseTransfer
    {
        return (new BraintreeTransactionResponseTransfer())
            ->setIsSuccess(false);
    }
}
