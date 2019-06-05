<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction;

use Braintree\Transaction as BraintreeTransaction;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;
use SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface;

class ShipmentRefundTransaction extends AbstractTransaction
{
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
        return ApiConstants::CREDIT;
    }

    /**
     * @return string
     */
    protected function getTransactionCode(): string
    {
        return ApiConstants::TRANSACTION_CODE_REFUND;
    }

    /**
     * @return \Braintree\Result\Successful|\Braintree\Result\Error|\Braintree\Transaction
     */
    protected function doTransaction()
    {
        return $this->refund();
    }

    /**
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    protected function refund()
    {
        return BraintreeTransaction::refund(
            $this->transactionMetaTransfer->getShipmentRefundTransactionId(),
            $this->transactionMetaTransfer->getRefundAmount()
        );
    }
}
