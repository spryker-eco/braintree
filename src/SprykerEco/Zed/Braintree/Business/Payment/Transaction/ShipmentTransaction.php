<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction;

use Braintree\Transaction as BraintreeTransaction;
use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;
use SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface;

class ShipmentTransaction extends AbstractTransaction
{
    protected const ATTRIBUTE_KEY_ORDER_ID = 'orderId';

    /**
     * @var \SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface
     */
    protected $braintreeEntityManager;

    /**
     * @param \SprykerEco\Zed\Braintree\BraintreeConfig $config
     * @param \SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface $braintreeEntityManager
     */
    public function __construct(
        BraintreeConfig $config,
        BraintreeEntityManagerInterface $braintreeEntityManager
    ) {
        parent::__construct($config);
        $this->braintreeEntityManager = $braintreeEntityManager;
    }

    /**
     * @return string
     */
    protected function getTransactionType(): string
    {
        return ApiConstants::SALE;
    }

    /**
     * @return string
     */
    protected function getTransactionCode(): string
    {
        return ApiConstants::TRANSACTION_CODE_CAPTURE;
    }

    /**
     * @return \Braintree\Result\Successful|\Braintree\Result\Error|\Braintree\Transaction
     */
    protected function doTransaction()
    {
        return $this->capture();
    }

    /**
     * @param \Braintree\Result\Error|\Braintree\Result\Successful $response
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    protected function afterTransaction($response): BraintreeTransactionResponseTransfer
    {
        if ($this->isTransactionSuccessful($response)) {
            $braintreeTransactionResponseTransfer = $this->getSuccessResponseTransfer($response);
            $this->logApiResponse($braintreeTransactionResponseTransfer, $this->getIdPayment(), $response->__get('transaction')->statusHistory);

            $this->braintreeEntityManager->updateIsShipmentOperationValue($this->getIdPayment(), $braintreeTransactionResponseTransfer->getTransactionId(), true);

            return $braintreeTransactionResponseTransfer;
        }

        $braintreeTransactionResponseTransfer = $this->getErrorResponseTransfer($response);
        $this->logApiResponse($braintreeTransactionResponseTransfer, $this->getIdPayment());

        return $braintreeTransactionResponseTransfer;
    }

    /**
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    protected function capture()
    {
        return BraintreeTransaction::submitForPartialSettlement(
            $this->getTransactionIdentifier(),
            $this->transactionMetaTransfer->getCaptureShipmentAmount(),
            [
                static::ATTRIBUTE_KEY_ORDER_ID => $this->transactionMetaTransfer->getOrderReference(),
            ]
        );
    }
}
