<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Service\Braintree\Model\TokenGenerator;

interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generateToken(): string;
}
