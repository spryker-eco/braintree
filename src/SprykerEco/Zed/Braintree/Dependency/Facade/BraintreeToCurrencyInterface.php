<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Braintree\Dependency\Facade;

interface BraintreeToCurrencyInterface
{

    /**
     * @return \Generated\Shared\Transfer\CurrencyTransfer
     */
    public function getCurrent();

}
