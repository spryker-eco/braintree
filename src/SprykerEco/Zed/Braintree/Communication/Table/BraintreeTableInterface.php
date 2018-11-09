<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Communication\Table;

interface BraintreeTableInterface
{
    /**
     * @return string
     */
    public function render();

    /**
     * @return array
     */
    public function fetchData();
}
