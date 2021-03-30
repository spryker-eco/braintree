<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business\Order;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\PaymentBraintreeBuilder;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\BraintreePaymentTransfer;
use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintree;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeQuery;
use Orm\Zed\Country\Persistence\SpyCountry;
use Orm\Zed\Country\Persistence\SpyCountryQuery;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomer;
use Orm\Zed\Oms\Persistence\SpyOmsOrderItemState;
use Orm\Zed\Oms\Persistence\SpyOmsOrderProcess;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderAddress;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use SprykerEco\Shared\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Order\Saver;
use SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManager;

/**
 * Auto-generated group annotations
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Order
 * @group SaverTest
 * Add your own group annotations below this line
 */
class SaverTest extends Unit
{
    /**
     * @return void
     */
    public function testSaveOrderPaymentCreatesPersistentPaymentData()
    {
        $saveOrderTransfer = $this->createSaveOrderTransfer();
        $quoteTransfer = $this->getQuoteTransfer($saveOrderTransfer);
        $orderManager = new Saver(new BraintreeEntityManager());

        $orderManager->saveOrderPayment($quoteTransfer, $saveOrderTransfer);

        $paymentEntity = SpyPaymentBraintreeQuery::create()->findOneByFkSalesOrder(
            $saveOrderTransfer->getIdSalesOrder()
        );
        $this->assertInstanceOf(SpyPaymentBraintree::class, $paymentEntity);

        $paymentOrderItemEntities = $paymentEntity->getSpyPaymentBraintreeOrderItems();
        $this->assertCount(1, $paymentOrderItemEntities);
    }

    /**
     * @return void
     */
    public function testSaveOrderPaymentHasAddressData()
    {
        $saveOrderTransfer = $this->createSaveOrderTransfer();
        $quoteTransfer = $this->getQuoteTransfer($saveOrderTransfer);
        $orderManager = new Saver(new BraintreeEntityManager());

        $orderManager->saveOrderPayment($quoteTransfer, $saveOrderTransfer);

        $paymentTransfer = $quoteTransfer->getPayment()->getBraintree();
        $addressTransfer = $paymentTransfer->getBillingAddress();

        $paymentEntity = SpyPaymentBraintreeQuery::create()->findOneByFkSalesOrder($saveOrderTransfer->getIdSalesOrder());
        $this->assertEquals($addressTransfer->getCity(), $paymentEntity->getCity());
        $this->assertEquals($addressTransfer->getIso2Code(), $paymentEntity->getCountryIso2Code());
        $this->assertEquals($addressTransfer->getZipCode(), $paymentEntity->getZipCode());
        $this->assertEquals(
            trim(sprintf(
                '%s %s %s',
                $addressTransfer->getAddress1(),
                $addressTransfer->getAddress2(),
                $addressTransfer->getAddress3()
            )),
            $paymentEntity->getStreet()
        );
    }

    /**
     * @return void
     */
    public function testUpdateOrderPaymentUpdatesPaymentData(): void
    {
        // Arrange
        $saveOrderTransfer = $this->createSaveOrderTransfer();
        $quoteTransfer = $this->getQuoteTransfer($saveOrderTransfer);
        $braintreePaymentType = $quoteTransfer
            ->getPayment()
            ->getBraintree()
            ->getPaymentType();

        $idSalesOrder = $saveOrderTransfer->getIdSalesOrder();
        $paymentEntity = $this->createPaymentEntity($idSalesOrder);

        $this->assertNotSame($braintreePaymentType, $paymentEntity->getPaymentType());

        $orderManager = new Saver(new BraintreeEntityManager());
        $orderManager->updateOrderPayment($quoteTransfer, $saveOrderTransfer);

        // Act
        $paymentEntity = SpyPaymentBraintreeQuery::create()->findOneByFkSalesOrder($idSalesOrder);

        // Asset
        $this->assertSame($braintreePaymentType, $paymentEntity->getPaymentType());
    }

    /**
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function getQuoteTransfer(SaveOrderTransfer $saveOrderTransfer)
    {
        $orderEntity = $this->createOrderEntity();

        $paymentAddressTransfer = new AddressTransfer();
        $email = 'testst@tewst.com';
        $paymentAddressTransfer
            ->setIso2Code('DE')
            ->setEmail($email)
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setCellPhone('+40 175 0815')
            ->setPhone('+30 0815')
            ->setAddress1('Straße des 17. Juni')
            ->setAddress2('135')
            ->setZipCode('10623')
            ->setCity('Berlin');

        $braintreePaymentTransfer = new BraintreePaymentTransfer();
        $braintreePaymentTransfer
            ->setEmail($email)
            ->setDateOfBirth('1970-01-02')
            ->setClientIp('127.0.0.1')
            ->setAccountBrand(BraintreeConfig::PAYMENT_METHOD_PAY_PAL)
            ->setLanguageIso2Code('DE')
            ->setCurrencyIso3Code('EUR')
            ->setPaymentType('credit_card')
            ->setBillingAddress($paymentAddressTransfer);

        $quoteTransfer = new QuoteTransfer();

        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setEmail($email);
        $customerTransfer->setIsGuest(true);
        $quoteTransfer->setCustomer($customerTransfer);

        $saveOrderTransfer->setIdSalesOrder($orderEntity->getIdSalesOrder());

        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setBraintree($braintreePaymentTransfer);
        $paymentTransfer->setPaymentProvider(BraintreeConfig::PROVIDER_NAME);
        $paymentTransfer
            ->setBraintreeTransactionResponse((new BraintreeTransactionResponseTransfer())->setIsSuccess(true));

        $quoteTransfer->setPayment($paymentTransfer);

        foreach ($orderEntity->getItems() as $orderItemEntity) {
            $itemTransfer = new ItemTransfer();
            $itemTransfer
                ->setName($orderItemEntity->getName())
                ->setQuantity($orderItemEntity->getQuantity())
                ->setUnitGrossPrice($orderItemEntity->getGrossPrice())
                ->setFkSalesOrder($orderItemEntity->getFkSalesOrder())
                ->setIdSalesOrderItem($orderItemEntity->getIdSalesOrderItem());
            $saveOrderTransfer->addOrderItem($itemTransfer);
        }

        return $quoteTransfer;
    }

    /**
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    protected function createOrderEntity()
    {
        $countryEntity = SpyCountryQuery::create()->filterByIso2Code('DE')->findOneOrCreate();
        $countryEntity->save();

        $billingAddressEntity = $this->createAndGetAddressEntity($countryEntity);
        $customerEntity = $this->createAndGetCustomerEntity();

        $orderEntity = $this->createAndGetOrderEntity($billingAddressEntity, $customerEntity);

        $this->createOrderItemEntity($orderEntity->getIdSalesOrder());

        return $orderEntity;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderItem
     */
    protected function createOrderItemEntity($idSalesOrder)
    {
        $stateEntity = $this->createOrderItemStateEntity();
        $processEntity = $this->createOrderProcessEntity();

        $orderItemEntity = new SpySalesOrderItem();
        $orderItemEntity
            ->setFkSalesOrder($idSalesOrder)
            ->setFkOmsOrderItemState($stateEntity->getIdOmsOrderItemState())
            ->setFkOmsOrderProcess($processEntity->getIdOmsOrderProcess())
            ->setName('test product')
            ->setSku('1324354657687980')
            ->setGrossPrice(1000)
            ->setQuantity(1);
        $orderItemEntity->save();

        return $orderItemEntity;
    }

    /**
     * @return \Orm\Zed\Oms\Persistence\SpyOmsOrderItemState
     */
    protected function createOrderItemStateEntity()
    {
        $stateEntity = new SpyOmsOrderItemState();
        $stateEntity->setName('test item state');
        $stateEntity->save();

        return $stateEntity;
    }

    /**
     * @return \Orm\Zed\Oms\Persistence\SpyOmsOrderProcess
     */
    protected function createOrderProcessEntity()
    {
        $processEntity = new SpyOmsOrderProcess();
        $processEntity->setName('test process');
        $processEntity->save();

        return $processEntity;
    }

    /**
     * @return \Generated\Shared\Transfer\SaveOrderTransfer
     */
    protected function createSaveOrderTransfer()
    {
        return new SaveOrderTransfer();
    }

    /**
     * @param \Orm\Zed\Country\Persistence\SpyCountry $countryEntity
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderAddress
     */
    protected function createAndGetAddressEntity(SpyCountry $countryEntity)
    {
        $billingAddressEntity = (new SpySalesOrderAddress())
            ->setFkCountry($countryEntity->getIdCountry())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setAddress1('Straße des 17. Juni 135')
            ->setCity('Berlin')
            ->setZipCode('10623');
        $billingAddressEntity->save();

        return $billingAddressEntity;
    }

    /**
     * @return \Orm\Zed\Customer\Persistence\SpyCustomer
     */
    protected function createAndGetCustomerEntity()
    {
        $customerEntity = (new SpyCustomer())
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setEmail('john@doe.com')
            ->setDateOfBirth('1970-01-01')
            ->setGender(SpyCustomerTableMap::COL_GENDER_MALE)
            ->setCustomerReference('braintree-pre-authorization-test');
        $customerEntity->save();

        return $customerEntity;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderAddress $billingAddressEntity
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customerEntity
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrder
     */
    protected function createAndGetOrderEntity(SpySalesOrderAddress $billingAddressEntity, SpyCustomer $customerEntity)
    {
        $orderEntity = (new SpySalesOrder())
            ->setEmail('john@doe.com')
            ->setIsTest(true)
            ->setFkSalesOrderAddressBilling($billingAddressEntity->getIdSalesOrderAddress())
            ->setFkSalesOrderAddressShipping($billingAddressEntity->getIdSalesOrderAddress())
            ->setCustomer($customerEntity)
            ->setOrderReference('foo-bar-baz-2');
        $orderEntity->save();

        return $orderEntity;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintree
     */
    protected function createPaymentEntity(int $idSalesOrder): SpyPaymentBraintree
    {
        $paymentBraintreeData = (new PaymentBraintreeBuilder())->build();

        $paymentEntity = new SpyPaymentBraintree();
        $paymentEntity->fromArray($paymentBraintreeData->toArray());
        $paymentEntity->setFkSalesOrder($idSalesOrder);
        $paymentEntity->save();

        return $paymentEntity;
    }
}
