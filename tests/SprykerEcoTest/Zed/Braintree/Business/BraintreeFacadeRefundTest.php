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
use Generated\Shared\Transfer\RefundTransfer;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\RefundItemsTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\RefundOrderTransaction;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeBridge;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group BraintreeFacadeRefundTest
 * Add your own group annotations below this line
 */
class BraintreeFacadeRefundTest extends AbstractFacadeTest
{
    /**
     * @return void
     */
    public function testRefundPaymentWithSuccessResponse()
    {
        $factoryMock = $this->getFactoryMock(['createRefundOrderTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundOrderTransaction')->willReturn(
            $this->getRefundOrderTransactionMock(),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->refundPayment([], $this->getOrderEntity());

        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testRefundPaymentWithFailureResponse()
    {
        $factoryMock = $this->getFactoryMock(['createRefundOrderTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundOrderTransaction')->willReturn(
            $this->getRefundOrderTransactionMock(false),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->refundPayment([], $this->getOrderEntity());

        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testRefundOrderPaymentWithSuccessResponse()
    {
        $factoryMock = $this->getFactoryMock(['createRefundOrderTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundOrderTransaction')->willReturn(
            $this->getRefundOrderTransactionMock(),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->refundOrderPayment([], $this->getOrderEntity());

        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testRefundOrderPaymentWithFailureResponse()
    {
        $factoryMock = $this->getFactoryMock(['createRefundOrderTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundOrderTransaction')->willReturn(
            $this->getRefundOrderTransactionMock(false),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $response = $braintreeFacade->refundOrderPayment([], $this->getOrderEntity());

        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testRefundItemsPaymentWithSuccessResponse()
    {
        $factoryMock = $this->getFactoryMock(['createRefundItemsTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundItemsTransaction')->willReturn(
            $this->getRefundItemsTransactionMock(),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $braintreeFacade->refundItemsPayment([], $this->getOrderEntity());
    }

    /**
     * @return void
     */
    public function testRefundItemsPaymentWithFailureResponse()
    {
        $factoryMock = $this->getFactoryMock(['createRefundItemsTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundItemsTransaction')->willReturn(
            $this->getRefundItemsTransactionMock(false),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);
        $braintreeFacade->refundItemsPayment([], $this->getOrderEntity());
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\RefundOrderTransaction
     */
    protected function getRefundOrderTransactionMock($success = true): RefundOrderTransaction
    {
        $moneyFacadeMock = $this->getMoneyFacadeMock();
        $refundTransactionMockBuilder = $this->getMockBuilder(RefundOrderTransaction::class);
        $refundTransactionMockBuilder->setMethods(['refund', 'initializeBraintree']);
        $refundTransactionMockBuilder->disableOriginalConstructor();
        $refundTransactionMockBuilder->setConstructorArgs([
            new BraintreeConfig(),
            new BraintreeToMoneyFacadeBridge($moneyFacadeMock),
        ]);

        if ($success) {
            $response = $this->getSuccessResponse();
        } else {
            $response = $this->getErrorResponse();
        }

        $refundTransactionMock = $refundTransactionMockBuilder->getMock();
        $refundTransactionMock->expects($this->once())
            ->method('refund')
            ->willReturn($response);

        return $refundTransactionMock;
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\RefundItemsTransaction
     */
    protected function getRefundItemsTransactionMock($success = true): RefundItemsTransaction
    {
        $moneyFacadeMock = $this->getMoneyFacadeMock();
        $refundTransactionMockBuilder = $this->getMockBuilder(RefundItemsTransaction::class);
        $refundTransactionMockBuilder->setMethods(['refund', 'initializeBraintree']);
        $refundTransactionMockBuilder->disableOriginalConstructor();
        $refundTransactionMockBuilder->setConstructorArgs([
            new BraintreeConfig(),
            new BraintreeToMoneyFacadeBridge($moneyFacadeMock),
        ]);

        if ($success) {
            $response = $this->getSuccessResponse();
        } else {
            $response = $this->getErrorResponse();
        }

        $refundTransactionMock = $refundTransactionMockBuilder->getMock();
        $refundTransactionMock->method('refund')
            ->willReturn($response);

        return $refundTransactionMock;
    }

    /**
     * @return \Braintree\Result\Successful
     */
    protected function getSuccessResponse()
    {
        $transaction = $this->getTransaction(ApiConstants::STATUS_CODE_CAPTURE_SUBMITTED);
        $response = new Successful([$transaction]);

        return $response;
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface
     */
    protected function getRefundFacadeMock($success = true): BraintreeToRefundFacadeInterface
    {
        $refundFacadeMock = $this->getMockBuilder(BraintreeToRefundFacadeInterface::class)->setMethods(['calculateRefund', 'saveRefund'])->getMock();
        $refundFacadeMock->expects($this->any())->method('calculateRefund')->willReturn(new RefundTransfer());

        return $refundFacadeMock;
    }

    /**
     * @param string $status
     *
     * @return \Braintree\Transaction
     */
    protected function getTransaction($status)
    {
        $transaction = Transaction::factory([
            'id' => 123,
            'processorResponseCode' => 1000,
            'processorResponseText' => 'Approved',
            'createdAt' => new DateTime(),
            'status' => $status,
            'type' => 'refund',
            'amount' => 10.00,
            'merchantAccountId' => 'abc',
            'statusHistory' => new StatusDetails([
                'timestamp' => new DateTime(),
                'status' => 'settling',
            ]),
        ]);

        return $transaction;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface
     */
    protected function getMoneyFacadeMock(): BraintreeToMoneyFacadeInterface
    {
        $moneyFacadeMock = $this->getMockBuilder(BraintreeToMoneyFacadeInterface::class)->getMock();

        return $moneyFacadeMock;
    }
}
