<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
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
 * @group CreditCardGuestHappyCaseCest
 * Add your own group annotations below this line
 */
class CreditCardGuestHappyCaseCest
{
    /**
     * @skip Broken because of new checkout
     *
     * @param \SprykerEcoTest\Zed\Braintree\BraintreePresentationTester $i
     *
     * @return void
     */
    public function closeCreditCardOrderHappyCase(BraintreePresentationTester $i)
    {
        $checkoutTester = $i->haveFriend('checkoutTester', BraintreeBraintreePresentationTester::class);
        $checkoutTester->does(function (CheckoutPresentationTester $i) {
            $i->addToCart(ProductDetailPage::URL);
            $i->checkoutWithCreditCardAsGuest();
        });
        $checkoutTester->leave();
        $i->wait(10);

        $i->amZed();
        $i->amLoggedInUser();
        $i->closeCreditCardOrderHappyCase();
    }
}
