<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree;

use Spryker\Yves\Currency\Plugin\CurrencyPlugin;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;

class BraintreeDependencyProvider extends AbstractBundleDependencyProvider
{
    public const PLUGIN_CURRENCY = 'PLUGIN_CURRENCY';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container|\Spryker\Zed\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container = $this->addCurrencyPlugin($container);

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
}
