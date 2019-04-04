<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree;

use Spryker\Yves\Currency\Plugin\CurrencyPlugin;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToQuoteClientBridge;
use SprykerEco\Yves\Braintree\Dependency\Service\BraintreeToUtilEncodingServiceBridge;

class BraintreeDependencyProvider extends AbstractBundleDependencyProvider
{
    public const PLUGIN_CURRENCY = 'PLUGIN_CURRENCY';
    public const CLIENT_QUOTE = 'QUOTE_CLIENT';
    public const SERVICE_UTIL_ENCODING = 'UTIL_ENCODING_SERVICE';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container|\Spryker\Zed\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container = $this->addCurrencyPlugin($container);
        $container = $this->addQuoteClient($container);
        $container = $this->addUtilEncodingService($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCurrencyPlugin(Container $container)
    {
        $container[static::PLUGIN_CURRENCY] = function (Container $container) {
            return new CurrencyPlugin();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addQuoteClient(Container $container): Container
    {
        $container[static::CLIENT_QUOTE] = function (Container $container) {
            return new BraintreeToQuoteClientBridge($container->getLocator()->quote()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addUtilEncodingService(Container $container): Container
    {
        $container[static::SERVICE_UTIL_ENCODING] = function (Container $container) {
            return new BraintreeToUtilEncodingServiceBridge($container->getLocator()->utilEncoding()->service());
        };

        return $container;
    }
}
