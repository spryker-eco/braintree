<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Braintree\Business;

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
class BraintreeFacadeIsQuotePaymentValidTest extends AbstractFacadeTest
{
    /**
     * @return void
     */
    public function testIsQuotePaymentValidWithSuccessfulResponse(): void
    {
        // Arrange
        $braintreeFacade = $this->getBraintreeFacade();
        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->getQuoteTransfer($orderTransfer);
        $checkoutResponseTransfer = $this->createMock(CheckoutResponseTransfer::class);
        $checkoutResponseTransfer->setIsSuccess(true);

        // Act
        $result = $braintreeFacade->isQuotePaymentValid($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testIsQuotePaymentValidWithErrorResponse(): void
    {
        // Arrange
        $braintreeFacade = $this->getBraintreeFacade();
        $orderTransfer = $this->createOrderTransfer();
        $quoteTransfer = $this->getQuoteTransfer($orderTransfer);
        $quoteTransfer->getPayment()->getBraintree()->setNonce(null);
        $checkoutResponseTransfer = $this->createMock(CheckoutResponseTransfer::class);
        $checkoutResponseTransfer->expects($this->once())
            ->method('setIsSuccess')
            ->with(false)
            ->willReturnSelf();

        // Act
        $result = $braintreeFacade->isQuotePaymentValid($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertFalse($result);
    }
}
