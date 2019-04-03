<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree;

use Spryker\Yves\Kernel\AbstractFactory;
use SprykerEco\Yves\Braintree\Form\CreditCardSubForm;
use SprykerEco\Yves\Braintree\Form\DataProvider\CreditCardDataProvider;
use SprykerEco\Yves\Braintree\Form\DataProvider\PayPalDataProvider;
use SprykerEco\Yves\Braintree\Form\PayPalSubForm;
use SprykerEco\Yves\Braintree\Handler\BraintreeHandler;
use SprykerEco\Yves\Braintree\Mapper\PaypalResponse\PaypalResponseMapper;
use SprykerEco\Yves\Braintree\Mapper\PaypalResponse\PaypalResponseMapperInterface;
use SprykerEco\Yves\Braintree\Processor\PaypalResponseProcessor;
use SprykerEco\Yves\Braintree\Processor\PaypalResponseProcessorInterface;

class BraintreeFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createPayPalForm()
    {
        return new PayPalSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createCreditCardForm()
    {
        return new CreditCardSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createPayPalFormDataProvider()
    {
        return new PayPalDataProvider();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createCreditCardFormDataProvider()
    {
        return new CreditCardDataProvider();
    }

    /**
     * @return \SprykerEco\Yves\Braintree\Handler\BraintreeHandlerInterface
     */
    public function createBraintreeHandler()
    {
        return new BraintreeHandler($this->getCurrencyPlugin());
    }

    /**
     * @return \Spryker\Yves\Currency\Plugin\CurrencyPluginInterface
     */
    public function getCurrencyPlugin()
    {
        return $this->getProvidedDependency(BraintreeDependencyProvider::PLUGIN_CURRENCY);
    }

    /**
     * @return \SprykerEco\Yves\Braintree\Processor\PaypalResponseProcessorInterface
     */
    public function createResponseProcessor(): PaypalResponseProcessorInterface
    {
        return new PaypalResponseProcessor(
            $this->createPaypalResponseMapper()
        );
    }

    /**
     * @return \SprykerEco\Yves\Braintree\Mapper\PaypalResponse\PaypalResponseMapperInterface
     */
    public function createPaypalResponseMapper(): PaypalResponseMapperInterface
    {
        return new PaypalResponseMapper();
    }
}
