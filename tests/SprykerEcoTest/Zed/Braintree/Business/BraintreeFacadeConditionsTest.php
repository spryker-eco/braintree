<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business;

use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group BraintreeFacadeConditionsTest
 * Add your own group annotations below this line
 */
class BraintreeFacadeConditionsTest extends AbstractFacadeTest
{
    /**
     * @var \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog
     */
    protected $transactionStatusLogEntity;

    /**
     * @var \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog
     */
    protected $transactionRequestLogEntity;

    /**
     * @skip
     *
     * @return void
     */
    public function testIsAuthorizationApproved(): void
    {
        // Arrange
        $this->setUpAuthorizationTestData();
        $orderTransfer = $this->createOrderTransfer();
        $facade = $this->getBraintreeFacade();

        // Act
        $response = $facade->isAuthorizationApproved($orderTransfer);

        // Assert
        $this->assertTrue($response);
    }

    /**
     * @skip
     *
     * @return void
     */
    public function testIsCaptureApproved(): void
    {
        // Arrange
        $this->setUpCaptureTestData();
        $orderTransfer = $this->createOrderTransfer();
        $facade = $this->getBraintreeFacade();

        // Act
        $response = $facade->isCaptureApproved($orderTransfer);

        // Assert
        $this->assertTrue($response);
    }

    /**
     * @skip
     *
     * @return void
     */
    public function testIsReversalApproved(): void
    {
        // Arrange
        $this->setUpReversalTestData();
        $orderTransfer = $this->createOrderTransfer();
        $facade = $this->getBraintreeFacade();

        // Act
        $response = $facade->isReversalApproved($orderTransfer);

        // Assert
        $this->assertTrue($response);
    }

    /**
     * @skip
     *
     * @return void
     */
    public function testIsRefundApproved(): void
    {
        // Arrange
        $this->setUpRefundTestData();
        $orderTransfer = $this->createOrderTransfer();
        $facade = $this->getBraintreeFacade();

        // Act
        $response = $facade->isRefundApproved($orderTransfer);

        // Assert
        $this->assertTrue($response);
    }

    /**
     * @return void
     */
    protected function setUpAuthorizationTestData(): void
    {
        $this->transactionRequestLogEntity = (new SpyPaymentBraintreeTransactionRequestLog())
            ->setFkPaymentBraintree($this->paymentEntity->getIdPaymentBraintree())
            ->setTransactionType('sale')
            ->setTransactionCode(ApiConstants::TRANSACTION_CODE_AUTHORIZE)
            ->setTransactionId('abc');
        $this->transactionRequestLogEntity->save();

        $this->transactionStatusLogEntity = (new SpyPaymentBraintreeTransactionStatusLog())
            ->setFkPaymentBraintree($this->paymentEntity->getIdPaymentBraintree())
            ->setCode('1000')
            ->setIsSuccess(true)
            ->setMessage('Approved')
            ->setTransactionType('sale')
            ->setTransactionCode(ApiConstants::TRANSACTION_CODE_AUTHORIZE)
            ->setTransactionStatus(ApiConstants::STATUS_CODE_AUTHORIZE)
            ->setTransactionId('abc');
        $this->transactionStatusLogEntity->save();
    }

    /**
     * @return void
     */
    protected function setUpCaptureTestData(): void
    {
        $this->transactionRequestLogEntity = (new SpyPaymentBraintreeTransactionRequestLog())
            ->setFkPaymentBraintree($this->paymentEntity->getIdPaymentBraintree())
            ->setTransactionType('sale')
            ->setTransactionCode(ApiConstants::TRANSACTION_CODE_CAPTURE)
            ->setTransactionId('c');
        $this->transactionRequestLogEntity->save();

        $this->transactionStatusLogEntity = (new SpyPaymentBraintreeTransactionStatusLog())
            ->setFkPaymentBraintree($this->paymentEntity->getIdPaymentBraintree())
            ->setCode('1000')
            ->setIsSuccess(true)
            ->setMessage('Approved')
            ->setTransactionType('sale')
            ->setTransactionCode(ApiConstants::TRANSACTION_CODE_CAPTURE)
            ->setTransactionStatus(ApiConstants::STATUS_CODE_CAPTURE)
            ->setTransactionId('c');
        $this->transactionStatusLogEntity->save();
    }

    /**
     * @return void
     */
    protected function setUpReversalTestData(): void
    {
        $this->transactionRequestLogEntity = (new SpyPaymentBraintreeTransactionRequestLog())
            ->setFkPaymentBraintree($this->paymentEntity->getIdPaymentBraintree())
            ->setTransactionType('sale')
            ->setTransactionCode(ApiConstants::TRANSACTION_CODE_REVERSAL)
            ->setTransactionId('r');
        $this->transactionRequestLogEntity->save();

        $this->transactionStatusLogEntity = (new SpyPaymentBraintreeTransactionStatusLog())
            ->setFkPaymentBraintree($this->paymentEntity->getIdPaymentBraintree())
            ->setCode('1000')
            ->setIsSuccess(true)
            ->setMessage('Approved')
            ->setTransactionType('sale')
            ->setTransactionCode(ApiConstants::TRANSACTION_CODE_REVERSAL)
            ->setTransactionStatus(ApiConstants::STATUS_CODE_REVERSAL)
            ->setTransactionId('r');
        $this->transactionStatusLogEntity->save();
    }

    /**
     * @return void
     */
    protected function setUpRefundTestData(): void
    {
        $this->transactionRequestLogEntity = (new SpyPaymentBraintreeTransactionRequestLog())
            ->setFkPaymentBraintree($this->paymentEntity->getIdPaymentBraintree())
            ->setTransactionType('credit')
            ->setTransactionCode(ApiConstants::TRANSACTION_CODE_REFUND)
            ->setTransactionId('ref');
        $this->transactionRequestLogEntity->save();

        $this->transactionStatusLogEntity = (new SpyPaymentBraintreeTransactionStatusLog())
            ->setFkPaymentBraintree($this->paymentEntity->getIdPaymentBraintree())
            ->setCode('1000')
            ->setIsSuccess(true)
            ->setMessage('Approved')
            ->setTransactionType('credit')
            ->setTransactionCode(ApiConstants::TRANSACTION_CODE_REFUND)
            ->setTransactionStatus(ApiConstants::STATUS_CODE_REFUND)
            ->setTransactionId('ref');
        $this->transactionStatusLogEntity->save();
    }
}
