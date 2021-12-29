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
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToMessengerClientBridge;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToPaymentClientBridge;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToQuoteClientBridge;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToShipmentClientBridge;
use SprykerEco\Yves\Braintree\Dependency\Service\BraintreeToUtilEncodingServiceBridge;
use SprykerShop\Yves\CheckoutPage\Plugin\ShipmentHandlerPlugin;
use SprykerShop\Yves\MoneyWidget\Plugin\MoneyPlugin;

/**
 * @method \SprykerEco\Yves\Braintree\BraintreeConfig getConfig()
 */
class BraintreeDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const PLUGIN_CURRENCY = 'PLUGIN_CURRENCY';

    /**
     * @var string
     */
    public const CLIENT_QUOTE = 'CLIENT_QUOTE';

    /**
     * @var string
     */
    public const CLIENT_PAYMENT = 'CLIENT_PAYMENT';

    /**
     * @var string
     */
    public const CLIENT_SHIPMENT = 'CLIENT_SHIPMENT';

    /**
     * @var string
     */
    public const CLIENT_GLOSSARY = 'CLIENT_GLOSSARY';

    /**
     * @var string
     */
    public const CLIENT_CALCULATION = 'CLIENT_CALCULATION';

    /**
     * @var string
     */
    public const CLIENT_COUNTRY = 'CLIENT_COUNTRY';

    /**
     * @var string
     */
    public const CLIENT_MESSENGER = 'CLIENT_MESSENGER';

    /**
     * @var string
     */
    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    /**
     * @var string
     */
    public const PLUGIN_MONEY = 'PLUGIN_MONEY';

    /**
     * @var string
     */
    public const PLUGIN_SHIPMENT_HANDLER = 'PLUGIN_SHIPMENT_HANDLER';

    /**
     * @var string
     */
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
        $container = $this->addMessengerClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCurrencyPlugin(Container $container): Container
    {
        $container->set(static::PLUGIN_CURRENCY, function () {
            return new CurrencyPlugin();
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addQuoteClient(Container $container): Container
    {
        $container->set(static::CLIENT_QUOTE, function (Container $container) {
            return new BraintreeToQuoteClientBridge($container->getLocator()->quote()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new BraintreeToUtilEncodingServiceBridge($container->getLocator()->utilEncoding()->service());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addPaymentClient(Container $container): Container
    {
        $container->set(static::CLIENT_PAYMENT, function (Container $container) {
            return new BraintreeToPaymentClientBridge($container->getLocator()->payment()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addShipmentClient(Container $container): Container
    {
        $container->set(static::CLIENT_SHIPMENT, function (Container $container) {
            return new BraintreeToShipmentClientBridge($container->getLocator()->shipment()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addGlossaryClient(Container $container): Container
    {
        $container->set(static::CLIENT_GLOSSARY, function (Container $container) {
            return new BraintreeToGlossaryClientBridge($container->getLocator()->glossary()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCalculationClient(Container $container): Container
    {
        $container->set(static::CLIENT_CALCULATION, function (Container $container) {
            return new BraintreeToCalculationClientBridge($container->getLocator()->calculation()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCountryClient(Container $container): Container
    {
        $container->set(static::CLIENT_COUNTRY, function (Container $container) {
            return new BraintreeToCountryClientBridge($container->getLocator()->country()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addStore(Container $container): Container
    {
        $container->set(static::STORE, function () {
            return Store::getInstance();
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addMoneyPlugin(Container $container): Container
    {
        $container->set(static::PLUGIN_MONEY, function () {
            return new MoneyPlugin();
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addShipmentHandlerPlugin(Container $container): Container
    {
        $container->set(static::PLUGIN_SHIPMENT_HANDLER, function () {
            return new ShipmentHandlerPlugin();
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addMessengerClient(Container $container): Container
    {
        $container->set(static::CLIENT_MESSENGER, function (Container $container) {
            return new BraintreeToMessengerClientBridge($container->getLocator()->messenger()->client());
        });

        return $container;
    }
}
