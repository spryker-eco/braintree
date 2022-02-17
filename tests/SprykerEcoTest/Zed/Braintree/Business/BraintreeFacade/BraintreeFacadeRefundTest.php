<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business\BraintreeFacade;

use Braintree\Transaction\StatusDetails;
use DateTime;
use Generated\Shared\Transfer\RefundTransfer;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\AbstractTransaction;
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
    public function testRefundPaymentWithSuccessResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createRefundOrderTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundOrderTransaction')->willReturn(
            $this->getRefundOrderTransactionMock(),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $response = $braintreeFacade->refundPayment([], $this->getOrderEntity());

        // Assert
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testRefundPaymentWithFailureResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createRefundOrderTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundOrderTransaction')->willReturn(
            $this->getRefundOrderTransactionMock(false),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $response = $braintreeFacade->refundPayment([], $this->getOrderEntity());

        // Assert
        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testRefundOrderPaymentWithSuccessResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createRefundOrderTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundOrderTransaction')->willReturn(
            $this->getRefundOrderTransactionMock(),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $response = $braintreeFacade->refundOrderPayment([], $this->getOrderEntity());

        // Assert
        $this->assertTrue($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testRefundOrderPaymentWithFailureResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createRefundOrderTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundOrderTransaction')->willReturn(
            $this->getRefundOrderTransactionMock(false),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $response = $braintreeFacade->refundOrderPayment([], $this->getOrderEntity());

        // Assert
        $this->assertFalse($response->getIsSuccess());
    }

    /**
     * @return void
     */
    public function testRefundItemsPaymentWithSuccessResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createRefundItemsTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundItemsTransaction')->willReturn(
            $this->getRefundItemsTransactionMock(),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $braintreeFacade->refundItemsPayment([], $this->getOrderEntity());
    }

    /**
     * @return void
     */
    public function testRefundItemsPaymentWithFailureResponse(): void
    {
        // Arrange
        $factoryMock = $this->getFactoryMock(['createRefundItemsTransaction', 'getRefundFacade']);
        $factoryMock->method('createRefundItemsTransaction')->willReturn(
            $this->getRefundItemsTransactionMock(false),
        );
        $factoryMock->method('getRefundFacade')->willReturn(
            $this->getRefundFacadeMock(false),
        );
        $braintreeFacade = $this->getBraintreeFacade($factoryMock);

        // Act
        $braintreeFacade->refundItemsPayment([], $this->getOrderEntity());
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\Payment\Transaction\AbstractTransaction
     */
    protected function getRefundOrderTransactionMock(bool $success = true): AbstractTransaction
    {
        /** @var \Spryker\Zed\Money\Business\MoneyFacadeInterface $moneyFacadeMock */
        $moneyFacadeMock = $this->getMoneyFacadeMock();
        $refundTransactionMockBuilder = $this->getMockBuilder(RefundOrderTransaction::class);
        $refundTransactionMockBuilder->setMethods(['refund', 'initializeBraintree']);
        $refundTransactionMockBuilder->disableOriginalConstructor();
        $refundTransactionMockBuilder->setConstructorArgs([
            new BraintreeConfig(),
            new BraintreeToMoneyFacadeBridge($moneyFacadeMock),
        ]);
        $refundTransactionMock = $refundTransactionMockBuilder->getMock();

        if (!$success) {
            $refundTransactionMock->expects($this->once())->method('refund')->willReturn($this->getErrorResponse());

            return $refundTransactionMock;
        }

        $response = $this->tester->getSuccessfulTransactionResponse([
            'status' => ApiConstants::STATUS_CODE_CAPTURE_SUBMITTED,
            'type' => 'refund',
            'amount' => 10.00,
            'statusHistory' => new StatusDetails([
                'timestamp' => new DateTime(),
                'status' => 'settling',
            ]),
        ]);

        $refundTransactionMock->expects($this->once())
            ->method('refund')
            ->willReturn($response);

        return $refundTransactionMock;
    }

    /**
     * @param bool $success
     *
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\RefundItemsTransaction|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getRefundItemsTransactionMock($success = true): RefundItemsTransaction
    {
        /** @var \Spryker\Zed\Money\Business\MoneyFacadeInterface $moneyFacadeMock */
        $moneyFacadeMock = $this->getMoneyFacadeMock();
        $refundTransactionMockBuilder = $this->getMockBuilder(RefundItemsTransaction::class);
        $refundTransactionMockBuilder->setMethods(['refund', 'initializeBraintree']);
        $refundTransactionMockBuilder->disableOriginalConstructor();
        $refundTransactionMockBuilder->setConstructorArgs([
            new BraintreeConfig(),
            new BraintreeToMoneyFacadeBridge($moneyFacadeMock),
        ]);
        $refundTransactionMock = $refundTransactionMockBuilder->getMock();

        if (!$success) {
            $refundTransactionMock->method('refund')->willReturn($this->getErrorResponse());

            return $refundTransactionMock;
        }

        $response = $this->tester->getSuccessfulTransactionResponse([
            'status' => ApiConstants::STATUS_CODE_CAPTURE_SUBMITTED,
            'type' => 'refund',
            'amount' => 10.00,
            'statusHistory' => new StatusDetails([
                'timestamp' => new DateTime(),
                'status' => 'settling',
            ]),
        ]);

        $refundTransactionMock->method('refund')->willReturn($response);

        return $refundTransactionMock;
    }

    /**
     * @param bool $success
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface
     */
    protected function getRefundFacadeMock($success = true): BraintreeToRefundFacadeInterface
    {
        $refundFacadeMock = $this->getMockBuilder(BraintreeToRefundFacadeInterface::class)
            ->setMethods(['calculateRefund', 'saveRefund'])
            ->getMock();
        $refundFacadeMock->expects($this->any())
            ->method('calculateRefund')
            ->willReturn(new RefundTransfer());

        return $refundFacadeMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface
     */
    protected function getMoneyFacadeMock(): BraintreeToMoneyFacadeInterface
    {
        return $this->getMockBuilder(BraintreeToMoneyFacadeInterface::class)->getMock();
    }
}
