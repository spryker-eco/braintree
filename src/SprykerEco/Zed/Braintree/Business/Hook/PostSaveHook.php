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
use SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface;

class PostSaveHook implements PostSaveHookInterface
{
    /**
     * @var \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface $queryContainer
     */
    public function __construct(BraintreeQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function postSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse)
    {
        $queryLog = $this->queryContainer->queryTransactionStatusLogBySalesOrderId($checkoutResponse->getSaveOrder()->getIdSalesOrder());
        $logRecord = $queryLog->findOne();

        if ($logRecord && $logRecord->getCode() != ApiConstants::PAYMENT_CODE_AUTHORIZE_SUCCESS) {
            $errorTransfer = new CheckoutErrorTransfer();
            $errorTransfer
                ->setErrorCode($logRecord->getCode())
                ->setMessage($logRecord->getMessage());

            $checkoutResponse->addError($errorTransfer);
        }

        return $checkoutResponse;
    }
}
