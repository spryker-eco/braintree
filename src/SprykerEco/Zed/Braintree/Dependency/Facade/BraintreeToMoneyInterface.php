<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Braintree\Dependency\Facade;

interface BraintreeToMoneyInterface
{

    /**
     * @param int $value
     *
     * @return float
     */
    public function convertIntegerToDecimal($value);

}
