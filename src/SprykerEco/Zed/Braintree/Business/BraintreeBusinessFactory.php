<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Zed\Braintree\BraintreeDependencyProvider;
use SprykerEco\Zed\Braintree\Business\Hook\PostSaveHook;
use SprykerEco\Zed\Braintree\Business\Hook\PostSaveHookInterface;
use SprykerEco\Zed\Braintree\Business\Log\TransactionStatusLog;
use SprykerEco\Zed\Braintree\Business\Log\TransactionStatusLogInterface;
use SprykerEco\Zed\Braintree\Business\Order\Saver;
use SprykerEco\Zed\Braintree\Business\Order\SaverInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Filter\PaypalExpressPaymentMethodFilter;
use SprykerEco\Zed\Braintree\Business\Payment\Filter\PaypalExpressPaymentMethodFilterInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\AuthorizeTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureItemsTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\CaptureOrderTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\AuthorizeTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\AuthorizeTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\CaptureItemsTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\CaptureItemsTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\CaptureOrderTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\CaptureOrderTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PaymentTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PaymentTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PreCheckTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PreCheckTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RefundItemsTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RefundItemsTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RefundOrderTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RefundOrderTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RevertTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RevertTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentRefundTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentRefundTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentTransactionHandler;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\PaymentTransactionMetaVisitor;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorComposite;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\PaymentTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\PreCheckTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\RefundItemsTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\RefundOrderTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\RevertTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\ShipmentRefundTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\ShipmentTransaction;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToSalesFacadeInterface;

/**
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Braintree\BraintreeConfig getConfig()
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface getEntityManager()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BraintreeBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\AuthorizeTransactionHandlerInterface
     */
    public function createAuthorizeTransactionHandler(): AuthorizeTransactionHandlerInterface
    {
        return new AuthorizeTransactionHandler(
            $this->createAuthorizeTransaction(),
            $this->createDefaultTransactionMetaVisitor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\CaptureItemsTransactionHandlerInterface
     */
    public function createCaptureItemsTransactionHandler(): CaptureItemsTransactionHandlerInterface
    {
        return new CaptureItemsTransactionHandler(
            $this->createCaptureItemsTransaction(),
            $this->createDefaultTransactionMetaVisitor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\CaptureOrderTransactionHandlerInterface
     */
    public function createCaptureOrderTransactionHandler(): CaptureOrderTransactionHandlerInterface
    {
        return new CaptureOrderTransactionHandler(
            $this->createCaptureOrderTransaction(),
            $this->createDefaultTransactionMetaVisitor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PreCheckTransactionHandlerInterface
     */
    public function createPreCheckTransactionHandler(): PreCheckTransactionHandlerInterface
    {
        return new PreCheckTransactionHandler(
            $this->createPreCheckTransaction(),
            $this->createDefaultTransactionMetaVisitor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\PaymentTransactionHandlerInterface
     */
    public function createPaymentTransactionHandler(): PaymentTransactionHandlerInterface
    {
        return new PaymentTransactionHandler(
            $this->createPaymentTransaction(),
            $this->createDefaultTransactionMetaVisitor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RefundOrderTransactionHandlerInterface
     */
    public function createRefundOrderTransactionHandler(): RefundOrderTransactionHandlerInterface
    {
        return new RefundOrderTransactionHandler(
            $this->createRefundOrderTransaction(),
            $this->createDefaultTransactionMetaVisitor(),
            $this->getRefundFacade(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RefundItemsTransactionHandlerInterface
     */
    public function createRefundItemsTransactionHandler(): RefundItemsTransactionHandlerInterface
    {
        return new RefundItemsTransactionHandler(
            $this->createRefundItemsTransaction(),
            $this->createDefaultTransactionMetaVisitor(),
            $this->getRefundFacade(),
            $this->getRepository(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\RevertTransactionHandlerInterface
     */
    public function createRevertTransactionHandler(): RevertTransactionHandlerInterface
    {
        return new RevertTransactionHandler(
            $this->createRevertTransaction(),
            $this->createDefaultTransactionMetaVisitor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Order\SaverInterface
     */
    public function createOrderSaver(): SaverInterface
    {
        return new Saver();
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Log\TransactionStatusLogInterface
     */
    public function createTransactionStatusLog(): TransactionStatusLogInterface
    {
        return new TransactionStatusLog($this->getRepository());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Hook\PostSaveHookInterface
     */
    public function createPostSaveHook(): PostSaveHookInterface
    {
        return new PostSaveHook($this->getRepository());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface
     */
    public function createAuthorizeTransaction(): TransactionInterface
    {
        return new AuthorizeTransaction($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface
     */
    public function createDefaultTransactionMetaVisitor(): TransactionMetaVisitorInterface
    {
        $transactionMetaVisitorComposite = $this->createTransactionMetaVisitorComposite();
        $transactionMetaVisitorComposite->addVisitor($this->createPaymentTransactionMetaVisitor());

        return $transactionMetaVisitorComposite;
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface
     */
    public function createTransactionMetaVisitorComposite(): TransactionMetaVisitorInterface
    {
        return new TransactionMetaVisitorComposite();
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface
     */
    public function createPaymentTransactionMetaVisitor(): TransactionMetaVisitorInterface
    {
        return new PaymentTransactionMetaVisitor($this->getRepository());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface
     */
    public function createCaptureOrderTransaction(): TransactionInterface
    {
        return new CaptureOrderTransaction($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface
     */
    public function createCaptureItemsTransaction(): TransactionInterface
    {
        return new CaptureItemsTransaction(
            $this->getConfig(),
            $this->getMoneyFacade(),
            $this->getRepository(),
            $this->getEntityManager(),
            $this->getSalesFacade(),
            $this->createShipmentTransactionHandler(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface
     */
    public function createShipmentTransaction(): TransactionInterface
    {
        return new ShipmentTransaction(
            $this->getConfig(),
            $this->getEntityManager(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface
     */
    public function createShipmentRefundTransaction(): TransactionInterface
    {
        return new ShipmentRefundTransaction(
            $this->getConfig(),
            $this->getEntityManager(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentTransactionHandlerInterface
     */
    public function createShipmentTransactionHandler(): ShipmentTransactionHandlerInterface
    {
        return new ShipmentTransactionHandler(
            $this->createShipmentTransaction(),
            $this->createDefaultTransactionMetaVisitor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentRefundTransactionHandlerInterface
     */
    public function createShipmentRefundTransactionHandler(): ShipmentRefundTransactionHandlerInterface
    {
        return new ShipmentRefundTransactionHandler(
            $this->createShipmentRefundTransaction(),
            $this->createDefaultTransactionMetaVisitor(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface
     */
    public function createPreCheckTransaction(): TransactionInterface
    {
        return new PreCheckTransaction($this->getConfig(), $this->getMoneyFacade());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface
     */
    public function createPaymentTransaction(): TransactionInterface
    {
        return new PaymentTransaction($this->getConfig(), $this->getMoneyFacade());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface
     */
    public function getMoneyFacade(): BraintreeToMoneyFacadeInterface
    {
        return $this->getProvidedDependency(BraintreeDependencyProvider::FACADE_MONEY);
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface
     */
    public function createRefundOrderTransaction(): TransactionInterface
    {
        return new RefundOrderTransaction(
            $this->getConfig(),
            $this->getMoneyFacade(),
            $this->createShipmentRefundTransactionHandler(),
            $this->getRepository(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface
     */
    public function createRefundItemsTransaction(): TransactionInterface
    {
        return new RefundItemsTransaction(
            $this->getConfig(),
            $this->getMoneyFacade(),
            $this->createShipmentRefundTransactionHandler(),
            $this->getRepository(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface
     */
    public function createRevertTransaction(): TransactionInterface
    {
        return new RevertTransaction($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeInterface
     */
    public function getRefundFacade(): BraintreeToRefundFacadeInterface
    {
        return $this->getProvidedDependency(BraintreeDependencyProvider::FACADE_REFUND);
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Business\Payment\Filter\PaypalExpressPaymentMethodFilterInterface
     */
    public function createPaypalExpressCheckoutPaymentMethod(): PaypalExpressPaymentMethodFilterInterface
    {
        return new PaypalExpressPaymentMethodFilter();
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToSalesFacadeInterface
     */
    public function getSalesFacade(): BraintreeToSalesFacadeInterface
    {
        return $this->getProvidedDependency(BraintreeDependencyProvider::FACADE_SALES);
    }
}
