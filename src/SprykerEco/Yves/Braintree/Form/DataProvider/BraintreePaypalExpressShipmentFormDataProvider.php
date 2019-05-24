<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Form\DataProvider;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;

/**
 * @method \SprykerEco\Yves\Braintree\BraintreeFactory getFactory()
 */
class BraintreePaypalExpressShipmentFormDataProvider extends AbstractPlugin implements StepEngineFormDataProviderInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    public function getData(AbstractTransfer $quoteTransfer): AbstractTransfer
    {
        return $this->getFactory()->createBraintreePaypalExpressShipmentFormDataProvider()->getData($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function getOptions(AbstractTransfer $quoteTransfer): array
    {
        return $this->getFactory()->createBraintreePaypalExpressShipmentFormDataProvider()->getOptions($quoteTransfer);
    }
}
