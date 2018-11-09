<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Presentation;

use SprykerEcoTest\Yves\Braintree\BraintreePresentationTester as CheckoutPresentationTester;
use SprykerEcoTest\Yves\Braintree\PageObject\ProductDetailPage;
use SprykerEcoTest\Zed\Braintree\BraintreePresentationTester;

/**
 * Auto-generated group annotations
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Presentation
 * @group CreditCardGuestRefundCest
 * Add your own group annotations below this line
 */
class CreditCardGuestRefundCest
{
    /**
     * @skip
     *
     * @param \SprykerEcoTest\Zed\Braintree\BraintreePresentationTester $i
     *
     * @return void
     */
    public function refundItemAndCloseOrder(BraintreePresentationTester $i)
    {
        $checkoutTester = $i->haveFriend('checkoutTester', CheckoutPresentationTester::class);
        $checkoutTester->does(function (CheckoutPresentationTester $i) {
            $i->addToCart(ProductDetailPage::URL);
            $i->checkoutWithCreditCardAsGuest();
        });
        $checkoutTester->leave();
        $i->wait(10);

        $i->amZed();
        $i->amLoggedInUser();
        $i->refundItemAndCloseOrder();
    }
}
