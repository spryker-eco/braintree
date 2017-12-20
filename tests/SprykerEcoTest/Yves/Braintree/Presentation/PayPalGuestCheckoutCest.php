<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Yves\Braintree\Presentation;

use SprykerEcoTest\Yves\Braintree\BraintreePresentationTester;
use SprykerEcoTest\Yves\Braintree\PageObject\ProductDetailPage;

/**
 * Auto-generated group annotations
 * @group SprykerEcoTest
 * @group Yves
 * @group Braintree
 * @group Presentation
 * @group PayPalGuestCheckoutCest
 * Add your own group annotations below this line
 */
class PayPalGuestCheckoutCest
{
    /**
     * @skip because of "Processor Network Unavailable - Try Again" response
     *
     * @param \SprykerEcoTest\Yves\Braintree\BraintreePresentationTester $i
     *
     * @return void
     */
    public function testPayPalCheckoutAsGuest(BraintreePresentationTester $i)
    {
        $i->wantToTest('That i can go through paypal checkout as guest');
        $i->addToCart(ProductDetailPage::URL);
        $i->checkoutWithPayPalAsGuest();
    }
}
