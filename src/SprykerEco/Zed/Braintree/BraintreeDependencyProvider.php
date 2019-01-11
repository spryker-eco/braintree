<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToCurrencyFacadeBridge;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeBridge;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToRefundFacadeBridge;

class BraintreeDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_CURRENCY = 'FACADE_CURRENCY';
    public const FACADE_MONEY = 'FACADE_MONEY';
    public const FACADE_REFUND = 'FACADE_REFUND';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addRefundFacade($container);
        $container = $this->addCurrencyFacade($container);
        $container = $this->addMoneyFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addRefundFacade(Container $container)
    {
        $container[static::FACADE_REFUND] = function (Container $container) {
            return new BraintreeToRefundFacadeBridge($container->getLocator()->refund()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCurrencyFacade(Container $container)
    {
        $container[static::FACADE_CURRENCY] = function (Container $container) {
            return new BraintreeToCurrencyFacadeBridge($container->getLocator()->currency()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMoneyFacade(Container $container)
    {
        $container[static::FACADE_MONEY] = function (Container $container) {
            return new BraintreeToMoneyFacadeBridge($container->getLocator()->money()->facade());
        };

        return $container;
    }
}
