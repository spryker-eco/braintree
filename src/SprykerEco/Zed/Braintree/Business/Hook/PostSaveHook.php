<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Hook;

use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;
use SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface;

class PostSaveHook implements PostSaveHookInterface
{
    /**
     * @var \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface
     */
    protected $repository;

    /**
     * @param \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface $repository
     */
    public function __construct(BraintreeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function postSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse)
    {
        $paymentBraintreeTransactionStatusLogTransfer = $this->repository
            ->findPaymentBraintreeTransactionStatusLogQueryBySalesOrderId($checkoutResponse->getSaveOrder()->getIdSalesOrder());

        if ($paymentBraintreeTransactionStatusLogTransfer &&
            $paymentBraintreeTransactionStatusLogTransfer->getCode() != ApiConstants::PAYMENT_CODE_AUTHORIZE_SUCCESS) {
            $checkoutErrorTransfer = new CheckoutErrorTransfer();
            $checkoutErrorTransfer
                ->setErrorCode($paymentBraintreeTransactionStatusLogTransfer->getCode())
                ->setMessage($paymentBraintreeTransactionStatusLogTransfer->getMessage());

            $checkoutResponse->addError($checkoutErrorTransfer);
        }

        return $checkoutResponse;
    }
}
