<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business\BraintreeFacade;

use Generated\Shared\Transfer\CheckoutResponseTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group Braintree
 * @group Business
 * @group Facade
 * @group BraintreeFacadeIsQuotePaymentValidTest
 * Add your own group annotations below this line
 */
class IsQuotePaymentValidTest extends AbstractFacadeTest
{
    /**
     * @return void
     */
    public function testIsQuotePaymentValidWithSuccessfulResponse(): void
    {
        // Arrange
        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->createQuoteTransfer($orderTransfer);
        $checkoutResponseTransfer = $this->createMock(CheckoutResponseTransfer::class);
        $checkoutResponseTransfer->setIsSuccess(true);

        // Act
        $isValid = $this->getBraintreeFacade()->isQuotePaymentValid($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertTrue($isValid, 'Quote payment should be considered valid for successful checkout response');
    }

    /**
     * @return void
     */
    public function testIsQuotePaymentValidWithErrorResponse(): void
    {
        // Arrange
        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->createQuoteTransfer($orderTransfer);
        $quoteTransfer->getPayment()->getBraintree()->setNonce(null);
        $checkoutResponseTransfer = $this->createMock(CheckoutResponseTransfer::class);
        $checkoutResponseTransfer->expects($this->once())
            ->method('setIsSuccess')
            ->with(false)
            ->willReturnSelf();

        // Act
        $isValid = $this->getBraintreeFacade()->isQuotePaymentValid($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertFalse($isValid, 'Quote payment should not be considered valid for failed checkout response');
    }
}
