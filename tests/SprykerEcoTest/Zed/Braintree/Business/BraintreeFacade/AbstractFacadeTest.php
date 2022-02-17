<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business\BraintreeFacade;

use Braintree\Result\Error;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\BraintreePaymentTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Generated\Shared\Transfer\TransactionMetaTransfer;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintree;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery;
use Orm\Zed\Country\Persistence\SpyCountryQuery;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomerQuery;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderAddress;
use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Shared\Braintree\BraintreeConfig as SharedBraintreeConfig;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\BraintreeDependencyProvider;
use SprykerEco\Zed\Braintree\Business\BraintreeBusinessFactory;
use SprykerEco\Zed\Braintree\Business\BraintreeFacade;
use SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainer;
use SprykerEco\Zed\Braintree\Persistence\BraintreeRepository;
use SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group AbstractFacadeTest
 * Add your own group annotations below this line
 */
class AbstractFacadeTest extends Unit
{
    /**
     * @var \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    protected $orderEntity;

    /**
     * @var \Orm\Zed\Braintree\Persistence\SpyPaymentBraintree
     */
    protected $paymentEntity;

    /**
     * @var \SprykerEcoTest\Zed\Braintree\BraintreeBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->setUpSalesOrderTestData();
        $this->addPaymentTestData();
    }

    /**
     * @param \SprykerEco\Zed\Braintree\Business\BraintreeBusinessFactory|null $braintreeBusinessFactoryMock
     *
     * @return \SprykerEco\Zed\Braintree\Business\BraintreeFacade|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getBraintreeFacade(?BraintreeBusinessFactory $braintreeBusinessFactoryMock = null): BraintreeFacade
    {
        $braintreeFacade = new BraintreeFacade();

        if ($braintreeBusinessFactoryMock) {
            $braintreeFacade->setFactory($braintreeBusinessFactoryMock);
        }

        return $braintreeFacade;
    }

    /**
     * @return \Braintree\Result\Error
     */
    protected function getErrorResponse(): Error
    {
        return new Error(['errors' => [], 'message' => 'Error']);
    }

    /**
     * @return void
     */
    protected function setUpSalesOrderTestData(): void
    {
        $country = SpyCountryQuery::create()->filterByIso2Code('DE')->findOneOrCreate();
        $country->save();

        $billingAddress = (new SpySalesOrderAddress())
            ->setFkCountry($country->getIdCountry())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setAddress1('Straße des 17. Juni 135')
            ->setCity('Berlin')
            ->setZipCode('10623');

        $billingAddress->save();

        $customer = (new SpyCustomerQuery())
            ->filterByFirstName('John')
            ->filterByLastName('Doe')
            ->filterByEmail('john@doe.com')
            ->filterByDateOfBirth('1970-01-01')
            ->filterByGender(SpyCustomerTableMap::COL_GENDER_MALE)
            ->filterByCustomerReference('braintree-pre-authorization-test')
            ->findOneOrCreate();

        if ($customer->isNew()) {
            $customer->save();
        }

        $this->orderEntity = (new SpySalesOrderQuery())
            ->filterByEmail('john@doe.com')
            ->filterByIsTest(true)
            ->filterByFkSalesOrderAddressBilling($billingAddress->getIdSalesOrderAddress())
            ->filterByFkSalesOrderAddressShipping($billingAddress->getIdSalesOrderAddress())
            ->filterByFkCustomer($customer->getIdCustomer())
            ->filterByOrderReference('foo-bar-baz-2')
            ->findOneOrCreate();

        if ($this->orderEntity->isNew()) {
            $this->orderEntity->save();
        }

        /*$this->orderEntity = (new SpySalesOrder())
            ->setEmail('john@doe.com')
            ->setIsTest(true)
            ->setFkSalesOrderAddressBilling($billingAddress->getIdSalesOrderAddress())
            ->setFkSalesOrderAddressShipping($billingAddress->getIdSalesOrderAddress())
            ->setCustomer($customer)
            ->setOrderReference('foo-bar-baz-2');

        $this->orderEntity->save();*/
    }

    /**
     * @return void
     */
    protected function addPaymentTestData(): void
    {
        $this->paymentEntity = (new SpyPaymentBraintree())
            ->setFkSalesOrder($this->getOrderEntity()->getIdSalesOrder())
            ->setPaymentType(SharedBraintreeConfig::PAYMENT_METHOD_PAY_PAL)
            ->setTransactionId('abc')
            ->setClientIp('127.0.0.1')
            ->setEmail('jane@family-doe.org')
            ->setCountryIso2Code('DE')
            ->setCity('Berlin')
            ->setStreet('Straße des 17. Juni 135')
            ->setZipCode('10623')
            ->setLanguageIso2Code('DE')
            ->setCurrencyIso3Code('EUR');

        $this->paymentEntity->save();
    }

    /**
     * @return \Generated\Shared\Transfer\TransactionMetaTransfer
     */
    protected function getTransactionMetaTransfer(): TransactionMetaTransfer
    {
        $transactionMetaTransfer = new TransactionMetaTransfer();
        $transactionMetaTransfer->setIdSalesOrder($this->getOrderEntity()->getIdSalesOrder());

        return $transactionMetaTransfer;
    }

    /**
     * @param array $methods
     *
     * @return \SprykerEco\Zed\Braintree\Business\BraintreeBusinessFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getFactoryMock(array $methods): BraintreeBusinessFactory
    {
        $factoryMock = $this->getFactory($methods);
        $factoryMock->setContainer($this->getContainer());
        $factoryMock->setQueryContainer($this->getQueryContainerMock());
        $factoryMock->setRepository($this->getRepositoryMock());
        $factoryMock->setConfig(new BraintreeConfig());

        return $factoryMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainer
     */
    protected function getQueryContainerMock(): BraintreeQueryContainer
    {
        $queryContainerMock = $this->getMockBuilder(BraintreeQueryContainer::class)->getMock();

        $transactionStatusLogQueryMock = $this->getTransactionStatusLogQueryMock();

        $queryContainerMock->expects($this->any())
            ->method('queryPaymentBySalesOrderId')
            ->willReturn($transactionStatusLogQueryMock);

        return $queryContainerMock;
    }

    /**
     * @return \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface|\Spryker\Zed\Kernel\Persistence\AbstractRepository
     */
    protected function getRepositoryMock(): BraintreeRepositoryInterface
    {
        $queryContainerMock = $this->getMockBuilder(BraintreeRepository::class)->getMock();

        $paymentBraintreeTransfer = $this->getPaymentBraintreeTransfer();
        $paymentBraintreeTransactionStatusLogTransfer = $this->getPaymentBraintreeTransactionStatusLogTransfer();

        $queryContainerMock->expects($this->any())
            ->method('findPaymentBraintreeTransactionStatusLogQueryBySalesOrderId')
            ->willReturn($paymentBraintreeTransactionStatusLogTransfer);

        $queryContainerMock->expects($this->any())
            ->method('findPaymentBraintreeBySalesOrderId')
            ->willReturn($paymentBraintreeTransfer);

        return $queryContainerMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    private function getTransactionStatusLogQueryMock(): SpyPaymentBraintreeTransactionStatusLogQuery
    {
        $transactionStatusLogQueryMock = $this->getMockBuilder(SpyPaymentBraintreeTransactionStatusLogQuery::class)->getMock();
        $transactionStatusLogQueryMock->method('findOne')->willReturn($this->paymentEntity);

        return $transactionStatusLogQueryMock;
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransfer
     */
    public function getPaymentBraintreeTransfer(): PaymentBraintreeTransfer
    {
        return (new PaymentBraintreeTransfer())
            ->setFkSalesOrder($this->getOrderEntity()->getIdSalesOrder())
            ->setIdPaymentBraintree($this->paymentEntity->getIdPaymentBraintree())
            ->setPaymentType(SharedBraintreeConfig::PAYMENT_METHOD_PAY_PAL)
            ->setTransactionId('abc')
            ->setClientIp('127.0.0.1')
            ->setEmail('jane@family-doe.org')
            ->setCountryIso2Code('DE')
            ->setCity('Berlin')
            ->setStreet('Straße des 17. Juni 135')
            ->setZipCode('10623')
            ->setLanguageIso2Code('DE')
            ->setCurrencyIso3Code('EUR');
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer
     */
    public function getPaymentBraintreeTransactionStatusLogTransfer(): PaymentBraintreeTransactionStatusLogTransfer
    {
        return (new PaymentBraintreeTransactionStatusLogTransfer());
    }

    /**
     * @param array $methods
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\Braintree\Business\BraintreeBusinessFactory
     */
    protected function getFactory(array $methods): BraintreeBusinessFactory
    {
        return $this->getMockBuilder(BraintreeBusinessFactory::class)->setMethods($methods)->getMock();
    }

    /**
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function getContainer(): Container
    {
        $container = new Container();
        $braintreeDependencyProvider = new BraintreeDependencyProvider();
        $braintreeDependencyProvider->provideBusinessLayerDependencies($container);

        return $container;
    }

    /**
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function createOrderTransfer(): OrderTransfer
    {
        $orderTransfer = new OrderTransfer();
        $totalsTransfer = new TotalsTransfer();
        $totalsTransfer->setGrandTotal(1000);
        $orderTransfer->setTotals($totalsTransfer);
        $orderTransfer->setIdSalesOrder($this->orderEntity->getIdSalesOrder());

        $addressTransfer = new AddressTransfer();
        $orderTransfer->setBillingAddress($addressTransfer);
        $orderTransfer->setShippingAddress($addressTransfer);

        return $orderTransfer;
    }

    /**
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    protected function getOrderEntity(): SpySalesOrder
    {
        return $this->orderEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer(OrderTransfer $orderTransfer): QuoteTransfer
    {
        $quoteTransfer = (new QuoteTransfer())
            ->setCustomer(new CustomerTransfer())
            ->setBillingAddress($orderTransfer->getBillingAddress())
            ->setShippingAddress($orderTransfer->getShippingAddress())
            ->setOrderReference($orderTransfer->getOrderReference())
            ->setTotals($orderTransfer->getTotals());

        $paymentTransfer = (new PaymentTransfer())
            ->setPaymentSelection(SharedBraintreeConfig::PAYMENT_METHOD_PAY_PAL)
            ->setPaymentProvider(SharedBraintreeConfig::PROVIDER_NAME);

        $braintreeTransfer = (new BraintreePaymentTransfer())->setNonce('fake_valid_nonce');

        $paymentTransfer->setBraintree($braintreeTransfer);

        $quoteTransfer->setPayment($paymentTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransferWithEmptyPayment(OrderTransfer $orderTransfer): QuoteTransfer
    {
        $quoteTransfer = (new QuoteTransfer())
            ->setCustomer(new CustomerTransfer())
            ->setBillingAddress($orderTransfer->getBillingAddress())
            ->setShippingAddress($orderTransfer->getShippingAddress())
            ->setOrderReference($orderTransfer->getOrderReference())
            ->setTotals($orderTransfer->getTotals());

        return $quoteTransfer;
    }
}
