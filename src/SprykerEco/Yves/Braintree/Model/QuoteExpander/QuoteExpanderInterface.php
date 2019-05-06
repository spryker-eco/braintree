<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Model\QuoteExpander;

use Generated\Shared\Transfer\QuoteTransfer;
use Symfony\Component\HttpFoundation\Request;

interface QuoteExpanderInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $idShipmentMethod
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuoteWithShipmentMethod(Request $request, int $idShipmentMethod): QuoteTransfer;
}
