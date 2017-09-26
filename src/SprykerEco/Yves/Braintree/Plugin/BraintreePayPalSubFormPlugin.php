<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Braintree\Plugin;

use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface;

/**
 * @method \Spryker\Yves\Braintree\BraintreeFactory getFactory()
 */
class BraintreePayPalSubFormPlugin extends AbstractPlugin implements SubFormPluginInterface
{

    /**
     * @return \Spryker\Yves\Braintree\Form\PayPalSubForm
     */
    public function createSubForm()
    {
        return $this->getFactory()->createPayPalForm();
    }

    /**
     * @return \Spryker\Yves\Braintree\Form\DataProvider\PayPalDataProvider
     */
    public function createSubFormDataProvider()
    {
        return $this->getFactory()->createPayPalFormDataProvider();
    }

}
