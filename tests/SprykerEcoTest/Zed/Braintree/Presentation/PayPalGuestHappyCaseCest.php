<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Presentation;

use SprykerEcoTest\Zed\Braintree\BraintreePresentationTester as CheckoutPresentationTester;
use SprykerEcoTest\Zed\Braintree\BraintreePresentationTester;
use SprykerEcoTest\Zed\Braintree\PageObject\ProductDetailPage;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Presentation
 * @group PayPalGuestHappyCaseCest
 * Add your own group annotations below this line
 */
class PayPalGuestHappyCaseCest
{
    /**
     * @skip because OMS timeout is not handled properly
     *
     * @param \SprykerEcoTest\Zed\Braintree\BraintreePresentationTester $i
     *
     * @return void
     */
    public function closePayPalGuestOrderHappyCase(BraintreePresentationTester $i)
    {
        $checkoutTester = $i->haveFriend('checkoutTester', CheckoutPresentationTester::class);
        $checkoutTester->does(function (CheckoutPresentationTester $i) {
            $i->addToCart(ProductDetailPage::URL);
            $i->checkoutWithPayPalAsGuest();
        });
        $checkoutTester->leave();
        $i->wait(10);

        $i->amZed();
        $i->amLoggedInUser();
        $i->closePayPalOrderHappyCase();
    }
}
