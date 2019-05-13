<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree;

use Spryker\Shared\Kernel\Store;
use Spryker\Yves\Currency\Plugin\CurrencyPlugin;
use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToCalculationClientBridge;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToCountryClientBridge;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToGlossaryClientBridge;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToPaymentClientBridge;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToQuoteClientBridge;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToShipmentClientBridge;
use SprykerEco\Yves\Braintree\Dependency\Service\BraintreeToUtilEncodingServiceBridge;
use SprykerShop\Yves\CheckoutPage\Plugin\ShipmentHandlerPlugin;
use SprykerShop\Yves\MoneyWidget\Plugin\MoneyPlugin;

class BraintreeDependencyProvider extends AbstractBundleDependencyProvider
{
    public const PLUGIN_CURRENCY = 'PLUGIN_CURRENCY';

    public const CLIENT_QUOTE = 'CLIENT_QUOTE';
    public const CLIENT_PAYMENT = 'CLIENT_PAYMENT';
    public const CLIENT_SHIPMENT = 'CLIENT_SHIPMENT';
    public const CLIENT_GLOSSARY = 'CLIENT_GLOSSARY';
    public const CLIENT_CALCULATION = 'CLIENT_CALCULATION';
    public const CLIENT_COUNTRY = 'CLIENT_COUNTRY';

    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    public const PLUGIN_MONEY = 'PLUGIN_MONEY';
    public const PLUGIN_SHIPMENT_HANDLER = 'PLUGIN_SHIPMENT_HANDLER';

    public const STORE = 'STORE';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = $this->addCurrencyPlugin($container);
        $container = $this->addQuoteClient($container);
        $container = $this->addUtilEncodingService($container);
        $container = $this->addPaymentClient($container);
        $container = $this->addShipmentClient($container);
        $container = $this->addGlossaryClient($container);
        $container = $this->addMoneyPlugin($container);
        $container = $this->addCountryClient($container);
        $container = $this->addShipmentHandlerPlugin($container);
        $container = $this->addStore($container);
        $container = $this->addCalculationClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCurrencyPlugin(Container $container): Container
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

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addPaymentClient(Container $container): Container
    {
        $container[static::CLIENT_PAYMENT] = function (Container $container) {
            return new BraintreeToPaymentClientBridge($container->getLocator()->payment()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addShipmentClient(Container $container): Container
    {
        $container[static::CLIENT_SHIPMENT] = function (Container $container) {
            return new BraintreeToShipmentClientBridge($container->getLocator()->shipment()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addGlossaryClient(Container $container): Container
    {
        $container[static::CLIENT_GLOSSARY] = function (Container $container) {
            return new BraintreeToGlossaryClientBridge($container->getLocator()->glossary()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCalculationClient(Container $container): Container
    {
        $container[static::CLIENT_CALCULATION] = function (Container $container) {
            return new BraintreeToCalculationClientBridge($container->getLocator()->calculation()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCountryClient(Container $container): Container
    {
        $container[static::CLIENT_COUNTRY] = function (Container $container) {
            return new BraintreeToCountryClientBridge($container->getLocator()->country()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addStore(Container $container): Container
    {
        $container[static::STORE] = function () {
            return Store::getInstance();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addMoneyPlugin(Container $container): Container
    {
        $container[static::PLUGIN_MONEY] = function () {
            return new MoneyPlugin();
        };

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addShipmentHandlerPlugin(Container $container): Container
    {
        $container[static::PLUGIN_SHIPMENT_HANDLER] = function () {
            return new ShipmentHandlerPlugin();
        };

        return $container;
    }
}
