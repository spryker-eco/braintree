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
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\RevertTransaction;

/**
 * Auto-generated group annotations
 *
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
    public function testRevertPaymentWithSuccessResponse()
    {
        $factoryMock = $this->getFactoryMock(['createRevertTransaction']);
        $factoryMock->expects($this->once())->method('createRevertTransaction')->willReturn(
            $this->getRevertTransactionMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        $transactionMetaTransfer = $this->getTransactionMetaTransfer();

        $response = $braintreeFacade->revertPayment($transactionMetaTransfer);

        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testRevertPaymentWithErrorResponse()
    {
        $factoryMock = $this->getFactoryMock(['createRevertTransaction']);
        $factoryMock->expects($this->once())->method('createRevertTransaction')->willReturn(
            $this->getRevertTransactionMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        $transactionMetaTransfer = $this->getTransactionMetaTransfer();
        $response = $braintreeFacade->revertPayment($transactionMetaTransfer);

        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\RevertTransaction
     */
    protected function getRevertTransactionMock($success = true): RevertTransaction
    {
        $revertTransactionMock = $this
            ->getMockBuilder(RevertTransaction::class)
            ->setMethods(['revert', 'initializeBraintree'])
            ->setConstructorArgs([new BraintreeConfig()])
            ->getMock();

        if ($success) {
            $revertTransactionMock->method('revert')->willReturn($this->getSuccessResponse());
        } else {
            $revertTransactionMock->method('revert')->willReturn($this->getErrorResponse());
        }

        return $revertTransactionMock;
    }

    /**
     * @return \Braintree\Result\Successful
     */
    protected function getSuccessResponse()
    {
        $transaction = Transaction::factory([
            'id' => 123,
            'processorResponseCode' => '1000',
            'processorResponseText' => 'Approved',
            'createdAt' => new DateTime(),
            'status' => 'revert',
            'type' => 'sale',
            'amount' => $this->createOrderTransfer()->getTotals()->getGrandTotal() / 100,
            'merchantAccountId' => 'abc',
            'statusHistory' => new StatusDetails([
                'timestamp' => new DateTime(),
                'status' => 'voided',
            ]),
        ]);
        $response = new Successful([$transaction]);

        return $response;
    }
}
