<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Braintree;

use SprykerEco\Yves\Braintree\Form\CreditCardSubForm;
use SprykerEco\Yves\Braintree\Form\DataProvider\CreditCardDataProvider;
use SprykerEco\Yves\Braintree\Form\DataProvider\PayPalDataProvider;
use SprykerEco\Yves\Braintree\Form\PayPalSubForm;
use SprykerEco\Yves\Braintree\Handler\BraintreeHandler;
use Spryker\Yves\Kernel\AbstractFactory;

/**
 * @method \SprykerEco\Client\Braintree\BraintreeClientInterface getClient()
 */
class BraintreeFactory extends AbstractFactory
{

    /**
     * @return \SprykerEco\Yves\Braintree\Form\PayPalSubForm
     */
    public function createPayPalForm()
    {
        return new PayPalSubForm();
    }

    /**
     * @return \SprykerEco\Yves\Braintree\Form\CreditCardSubForm
     */
    public function createCreditCardForm()
    {
        return new CreditCardSubForm();
    }

    /**
     * @return \SprykerEco\Yves\Braintree\Form\DataProvider\PayPalDataProvider
     */
    public function createPayPalFormDataProvider()
    {
        return new PayPalDataProvider();
    }

    /**
     * @return \SprykerEco\Yves\Braintree\Form\DataProvider\CreditCardDataProvider
     */
    public function createCreditCardFormDataProvider()
    {
        return new CreditCardDataProvider();
    }

    /**
     * @return \SprykerEco\Yves\Braintree\Handler\BraintreeHandler
     */
    public function createBraintreeHandler()
    {
        return new BraintreeHandler($this->getClient(), $this->getCurrencyPlugin());
    }

    /**
     * @return \Spryker\Yves\Currency\Plugin\CurrencyPluginInterface
     */
    protected function getCurrencyPlugin()
    {
        return $this->getProvidedDependency(BraintreeDependencyProvider::PLUGIN_CURRENCY);
    }

}
