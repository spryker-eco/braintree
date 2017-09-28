<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Braintree\Plugin;

use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface;

/**
 * @method \SprykerEco\Yves\Braintree\BraintreeFactory getFactory()
 */
class BraintreeCreditCardSubFormPlugin extends AbstractPlugin implements SubFormPluginInterface
{

    /**
     * @return \SprykerEco\Yves\Braintree\Form\PayPalSubForm
     */
    public function createSubForm()
    {
        return $this->getFactory()->createCreditCardForm();
    }

    /**
     * @return \SprykerEco\Yves\Braintree\Form\DataProvider\CreditCardDataProvider
     */
    public function createSubFormDataProvider()
    {
        return $this->getFactory()->createCreditCardFormDataProvider();
    }

}
