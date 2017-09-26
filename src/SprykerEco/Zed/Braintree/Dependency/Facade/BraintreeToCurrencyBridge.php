<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Braintree\Dependency\Facade;

class BraintreeToCurrencyBridge implements BraintreeToCurrencyInterface
{

    /**
     * @var \Spryker\Zed\Currency\Business\CurrencyFacadeInterface
     */
    protected $currencyFacade;

    /**
     * @param \Spryker\Zed\Currency\Business\CurrencyFacadeInterface $currencyFacade
     */
    public function __construct($currencyFacade)
    {
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * @return \Generated\Shared\Transfer\CurrencyTransfer
     */
    public function getCurrent()
    {
        return $this->currencyFacade->getCurrent();
    }

}
