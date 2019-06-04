<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Persistence;

use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeOrderItemQuery;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeQuery;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLogQuery;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use SprykerEco\Zed\Braintree\Persistence\Mapper\BraintreePersistenceMapper;
use SprykerEco\Zed\Braintree\Persistence\Mapper\BraintreePersistenceMapperInterface;

/**
 * @method \SprykerEco\Zed\Braintree\BraintreeConfig getConfig()
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface getEntityManager()
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface getRepository()
 */
class BraintreePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \SprykerEco\Zed\Braintree\Persistence\Mapper\BraintreePersistenceMapperInterface
     */
    public function createBraintreePersistenceMapper(): BraintreePersistenceMapperInterface
    {
        return new BraintreePersistenceMapper();
    }

    /**
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeQuery
     */
    public function createPaymentBraintreeQuery()
    {
        return SpyPaymentBraintreeQuery::create();
    }

    /**
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLogQuery
     */
    public function createPaymentBraintreeTransactionStatusLogQuery()
    {
        return SpyPaymentBraintreeTransactionStatusLogQuery::create();
    }

    /**
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLogQuery
     */
    public function createPaymentBraintreeTransactionRequestLogQuery()
    {
        return SpyPaymentBraintreeTransactionRequestLogQuery::create();
    }

    /**
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeOrderItemQuery
     */
    public function createPaymentBraintreeOrderItemQuery(): SpyPaymentBraintreeOrderItemQuery
    {
        return SpyPaymentBraintreeOrderItemQuery::create();
    }
}
