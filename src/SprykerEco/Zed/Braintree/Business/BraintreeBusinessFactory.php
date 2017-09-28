<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Braintree\Business;

use SprykerEco\Zed\Braintree\BraintreeDependencyProvider;
use SprykerEco\Zed\Braintree\Business\Hook\PostSaveHook;
use SprykerEco\Zed\Braintree\Business\Log\TransactionStatusLog;
use SprykerEco\Zed\Braintree\Business\Order\Saver;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\AuthorizeTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\AuthorizeTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\CaptureTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PreCheckTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RefundTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RevertTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\PaymentTransactionMetaVisitor;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorComposite;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\PreCheckTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\RefundTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\RevertTransaction;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Braintree\BraintreeConfig getConfig()
 */
class BraintreeBusinessFactory extends AbstractBusinessFactory
{

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\AuthorizeTransactionHandler
     */
    public function createAuthorizeTransactionHandler()
    {
        return new AuthorizeTransactionHandler(
            $this->createAuthorizeTransaction(),
            $this->createDefaultTransactionMetaVisitor()
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\AuthorizeTransaction
     */
    protected function createAuthorizeTransaction()
    {
        return new AuthorizeTransaction($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorComposite
     */
    protected function createDefaultTransactionMetaVisitor()
    {
        $transactionMetaVisitorComposite = $this->createTransactionMetaVisitorComposite();
        $transactionMetaVisitorComposite->addVisitor($this->createPaymentTransactionMetaVisitor());

        return $transactionMetaVisitorComposite;
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorComposite
     */
    protected function createTransactionMetaVisitorComposite()
    {
        return new TransactionMetaVisitorComposite();
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\PaymentTransactionMetaVisitor
     */
    protected function createPaymentTransactionMetaVisitor()
    {
        return new PaymentTransactionMetaVisitor($this->getQueryContainer());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\CaptureTransactionHandler
     */
    public function createCaptureTransactionHandler()
    {
        return new CaptureTransactionHandler(
            $this->createCaptureTransaction(),
            $this->createDefaultTransactionMetaVisitor()
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureTransaction
     */
    protected function createCaptureTransaction()
    {
        return new CaptureTransaction($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PreCheckTransactionHandler
     */
    public function createPreCheckTransactionHandler()
    {
        return new PreCheckTransactionHandler(
            $this->createPreCheckTransaction(),
            $this->createDefaultTransactionMetaVisitor()
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\PreCheckTransaction
     */
    protected function createPreCheckTransaction()
    {
        return new PreCheckTransaction($this->getConfig(), $this->getMoneyFacade());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyInterface
     */
    protected function getMoneyFacade()
    {
        return $this->getProvidedDependency(BraintreeDependencyProvider::FACADE_MONEY);
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RefundTransactionHandler
     */
    public function createRefundTransactionHandler()
    {
        return new RefundTransactionHandler(
            $this->createRefundTransaction(),
            $this->createDefaultTransactionMetaVisitor(),
            $this->getRefundFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\RefundTransaction
     */
    protected function createRefundTransaction()
    {
        return new RefundTransaction($this->getConfig(), $this->getMoneyFacade());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RevertTransactionHandler
     */
    public function createRevertTransactionHandler()
    {
        return new RevertTransactionHandler(
            $this->createRevertTransaction(),
            $this->createDefaultTransactionMetaVisitor()
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\RevertTransaction
     */
    protected function createRevertTransaction()
    {
        return new RevertTransaction($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundInterface
     */
    protected function getRefundFacade()
    {
        return $this->getProvidedDependency(BraintreeDependencyProvider::FACADE_REFUND);
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Order\SaverInterface
     */
    public function createOrderSaver()
    {
        return new Saver();
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Log\TransactionStatusLogInterface
     */
    public function createTransactionStatusLog()
    {
        return new TransactionStatusLog($this->getQueryContainer());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Hook\PostSaveHookInterface
     */
    public function createPostSaveHook()
    {
        return new PostSaveHook($this->getQueryContainer());
    }

}
