<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business;

use Braintree\Result\Successful;
use Braintree\Transaction;
use Braintree\Transaction\CreditCardDetails;
use Braintree\Transaction\StatusDetails;
use DateTime;
use Generated\Shared\Transfer\BraintreePaymentTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use SprykerEco\Shared\Braintree\BraintreeConfig as SharedBraintreeConfig;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Order\Saver;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\PaymentTransaction;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeBridge;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;

/**
 * Auto-generated group annotations
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group BraintreeFacadeIsQuotePaymentValidTest
 * Add your own group annotations below this line
 */
class BraintreeFacadeIsQuotePaymentValidTest extends AbstractFacadeTest
{
    /**
     * @return void
     */
    public function testIsQuotePaymentValidWithSuccessfulResponse()
    {
        $braintreeFacade = $this->getBraintreeFacade();

        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->getQuoteTransfer($orderTransfer);
        $result = $braintreeFacade->isQuotePaymentValid($quoteTransfer);

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testIsQuotePaymentValidWithErrorResponse()
    {
        $braintreeFacade = $this->getBraintreeFacade();

        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->getQuoteTransfer($orderTransfer);
        $quoteTransfer->getPayment()->getBraintree()->setNonce(null);

        $result = $braintreeFacade->isQuotePaymentValid($quoteTransfer);

        $this->assertFalse($result);
    }
}
