<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Service\Braintree;

interface BraintreeServiceInterface
{
    /**
     * Specification:
     * - Generates a token for initialising a payment via js library
     *
     * @api
     *
     * @return string
     */
    public function generateToken(): string;
}
