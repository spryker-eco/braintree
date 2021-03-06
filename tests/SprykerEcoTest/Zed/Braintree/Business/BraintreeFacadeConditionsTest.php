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
    public function testIsAuthorizationApproved()
    {
        $this->setUpAuthorizationTestData();

        $orderTransfer = $this->createOrderTransfer();
        $facade = $this->getBraintreeFacade();
        $response = $facade->isAuthorizationApproved($orderTransfer);
        $this->assertTrue($response);
    }

    /**
     * @skip
     *
     * @return void
     */
    public function testIsCaptureApproved()
    {
        $this->setUpCaptureTestData();

        $orderTransfer = $this->createOrderTransfer();
        $facade = $this->getBraintreeFacade();
        $response = $facade->isCaptureApproved($orderTransfer);
        $this->assertTrue($response);
    }

    /**
     * @skip
     *
     * @return void
     */
    public function testIsReversalApproved()
    {
        $this->setUpReversalTestData();

        $orderTransfer = $this->createOrderTransfer();
        $facade = $this->getBraintreeFacade();
        $response = $facade->isReversalApproved($orderTransfer);
        $this->assertTrue($response);
    }

    /**
     * @skip
     *
     * @return void
     */
    public function testIsRefundApproved()
    {
        $this->setUpRefundTestData();

        $orderTransfer = $this->createOrderTransfer();
        $facade = $this->getBraintreeFacade();
        $response = $facade->isRefundApproved($orderTransfer);
        $this->assertTrue($response);
    }

    /**
     * @return void
     */
    protected function setUpAuthorizationTestData()
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
    protected function setUpCaptureTestData()
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
    protected function setUpReversalTestData()
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
    protected function setUpRefundTestData()
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
