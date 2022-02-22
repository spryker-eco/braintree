<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business;

use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Generated\Shared\Transfer\TransactionMetaTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;

interface BraintreeFacadeInterface
{
    /**
     * Specification:
     * - Saves order payment method data according to quote and checkout response transfer data.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer);

    /**
     * Specification:
     * - Sends pre-authorize payment request to Braintree gateway to retrieve transaction data.
     * - Checks that form data matches transaction response data
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function preCheckPayment(QuoteTransfer $quoteTransfer);

    /**
     * Specification:
     * - Creates transaction on Braintree side and stores the response in quote.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function createPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer);

    /**
     * Specification:
     * - Processes payment confirmation request to Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function authorizePayment(TransactionMetaTransfer $transactionMetaTransfer);

    /**
     * Specification:
     * - Processes capture payment request to Braintree gateway.
     *
     * @api
     *
     * @deprecated Use `\SprykerEco\Zed\Braintree\Business\BraintreeFacadeInterface::captureOrderPayment()` instead.
     *
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function capturePayment(TransactionMetaTransfer $transactionMetaTransfer);

    /**
     * Specification:
     * - Processes capture order payment request to Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function captureOrderPayment(TransactionMetaTransfer $transactionMetaTransfer): BraintreeTransactionResponseTransfer;

    /**
     * Specification:
     * - Processes capture items payment request to Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function captureItemsPayment(TransactionMetaTransfer $transactionMetaTransfer): BraintreeTransactionResponseTransfer;

    /**
     * Specification:
     * - Processes cancel payment request to Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function revertPayment(TransactionMetaTransfer $transactionMetaTransfer);

    /**
     * Specification:
     * - Calculate RefundTransfer for given $salesOrderItems and $salesOrderEntity.
     * - Processes refund request to Braintree gateway by calculated RefundTransfer.
     *
     * @api
     *
     * @deprecated Use `\SprykerEco\Zed\Braintree\Business\BraintreeFacadeInterface::refundOrderPayment()` instead.
     *
     * @param array<\Orm\Zed\Sales\Persistence\SpySalesOrderItem> $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function refundPayment(array $salesOrderItems, SpySalesOrder $salesOrderEntity);

    /**
     * Specification:
     * - Calculate RefundTransfer for given $salesOrderItems and $salesOrderEntity.
     * - Processes refund request to Braintree gateway by calculated RefundTransfer.
     *
     * @api
     *
     * @param array $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function refundOrderPayment(array $salesOrderItems, SpySalesOrder $salesOrderEntity): BraintreeTransactionResponseTransfer;

    /**
     * Specification:
     * - Calculate RefundTransfer for given TransactionMetaTransfer.
     * - Processes refund requests to Braintree gateway by calculated RefundTransfer for every order item.
     *
     * @api
     *
     * @param array $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     *
     * @return void
     */
    public function refundItemsPayment(array $salesOrderItems, SpySalesOrder $salesOrderEntity): void;

    /**
     * Specification:
     * - Checks if pre-authorization API request got success response from Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isAuthorizationApproved(OrderTransfer $orderTransfer);

    /**
     * Specification:
     * - Checks if cancel API request got success response from Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isReversalApproved(OrderTransfer $orderTransfer);

    /**
     * Specification:
     * - Checks if capture API request got success response from Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isCaptureApproved(OrderTransfer $orderTransfer);

    /**
     * Specification:
     * - Checks if refund API request got success response from Braintree gateway.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return bool
     */
    public function isRefundApproved(OrderTransfer $orderTransfer);

    /**
     * Specification:
     * - Updates `CheckoutResponseTransfer` and `QuoteTransfer` accordingly to API response.
     * - If API request is successful - updates order payment method data according to `QuoteTransfer`.
     *
     * @api
     *
     * @deprecated Use `\SprykerEco\Zed\Braintree\Business\BraintreeFacadeInterface::checkoutPostSaveHook()` instead.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function postSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse);

    /**
     * Specification:
     * - Filter array object of payments for not showing PaypalExpress payment method on checkout step.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function filterPaypalExpressPaymentMethod(PaymentMethodsTransfer $paymentMethodsTransfer, QuoteTransfer $quoteTransfer): PaymentMethodsTransfer;

    /**
     * Specification:
     * - Requires `QuoteTransfer.payment.braintree` to be set.
     * - Returns `true` if payment provider is not Braintree.
     * - Returns `true` if Braintree has a nonce, adds an error message to `CheckoutResponseTransfer` and returns `false` otherwise.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function isQuotePaymentValid(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool;

    /**
     * Specification:
     * - Requires `SaveOrderTransfer.idSalesOrder` to be set.
     * - Requires `QuoteTransfer.payment` to be set.
     * - Executes Braintree sale API request.
     * - Updates `CheckoutResponseTransfer` and `QuoteTransfer` accordingly to API response.
     * - If API request is successful - updates order payment method data according to `QuoteTransfer`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function executeCheckoutPostSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse): CheckoutResponseTransfer;
}
