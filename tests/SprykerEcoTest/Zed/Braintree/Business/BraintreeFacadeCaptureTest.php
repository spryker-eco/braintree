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
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureTransaction;

/**
 * Auto-generated group annotations
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
    public function testCapturePaymentWithSuccessResponse()
    {
        $factoryMock = $this->getFactoryMock(['createCaptureTransaction']);
        $factoryMock->method('createCaptureTransaction')->willReturn(
            $this->getCaptureTransactionMock()
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->capturePayment($this->getTransactionMetaTransfer());

        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testCapturePaymentWithFailureResponse()
    {
        $factoryMock = $this->getFactoryMock(['createCaptureTransaction']);
        $factoryMock->method('createCaptureTransaction')->willReturn(
            $this->getCaptureTransactionMock(false)
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->capturePayment($this->getTransactionMetaTransfer());

        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureTransaction
     */
    protected function getCaptureTransactionMock($success = true)
    {
        $captureTransactionMock = $this
            ->getMockBuilder(CaptureTransaction::class)
            ->setMethods(['capture', 'initializeBraintree'])
            ->setConstructorArgs(
                [new BraintreeConfig()]
            )
            ->getMock();

        if ($success) {
            $captureTransactionMock->method('capture')->willReturn(
                $this->getSuccessResponse()
            );
        } else {
            $captureTransactionMock->method('capture')->willReturn(
                $this->getErrorResponse()
            );
        }

        return $captureTransactionMock;
    }

    /**
     * @return \Braintree\Result\Successful
     */
    protected function getSuccessResponse()
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
}
