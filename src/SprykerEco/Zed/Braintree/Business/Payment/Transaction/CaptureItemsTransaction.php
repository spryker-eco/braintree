<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction;

use Braintree\Transaction as BraintreeTransaction;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;

class CaptureItemsTransaction extends AbstractTransaction
{
    /**
     * @var \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * @param \SprykerEco\Zed\Braintree\BraintreeConfig $config
     * @param \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface $moneyFacade
     */
    public function __construct(
        BraintreeConfig $config,
        BraintreeToMoneyFacadeInterface $moneyFacade
    ) {
        parent::__construct($config);
        $this->moneyFacade = $moneyFacade;
    }

    /**
     * @return string
     */
    protected function getTransactionType()
    {
        return ApiConstants::SALE;
    }

    /**
     * @return string
     */
    protected function getTransactionCode()
    {
        return ApiConstants::TRANSACTION_CODE_CAPTURE;
    }

    /**
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    protected function doTransaction()
    {
        return $this->capture();
    }

    /**
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    protected function capture()
    {
        return BraintreeTransaction::submitForPartialSettlement(
            $this->getTransactionIdentifier(),
            $this->getDecimalAmountValueFromInt($this->transactionMetaTransfer->getCaptureAmount())
        );
    }

    /**
     * @param int $amount
     *
     * @return float
     */
    protected function getDecimalAmountValueFromInt(int $amount): float
    {
        return $this->moneyFacade->convertIntegerToDecimal($amount);
    }
}
