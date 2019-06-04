<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction;

use Braintree\Transaction as BraintreeTransaction;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer;
use Spryker\Shared\Shipment\ShipmentConstants;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentRefundTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;
use SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface;

class RefundOrderTransaction extends AbstractTransaction
{
    /**
     * @var \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * @var \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentRefundTransactionHandlerInterface
     */
    protected $shipmentRefundTransactionHandler;

    /**
     * @var \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface
     */
    protected $braintreeRepository;

    /**
     * @param \SprykerEco\Zed\Braintree\BraintreeConfig $brainTreeConfig
     * @param \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface $moneyFacade
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentRefundTransactionHandlerInterface $shipmentRefundTransactionHandler
     * @param \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface $braintreeRepository
     */
    public function __construct(
        BraintreeConfig $brainTreeConfig,
        BraintreeToMoneyFacadeInterface $moneyFacade,
        ShipmentRefundTransactionHandlerInterface $shipmentRefundTransactionHandler,
        BraintreeRepositoryInterface $braintreeRepository
    ) {
        parent::__construct($brainTreeConfig);
        $this->moneyFacade = $moneyFacade;
        $this->shipmentRefundTransactionHandler = $shipmentRefundTransactionHandler;
        $this->braintreeRepository = $braintreeRepository;
    }

    /**
     * @return string
     */
    protected function getTransactionType()
    {
        return ApiConstants::CREDIT;
    }

    /**
     * @return string
     */
    protected function getTransactionCode()
    {
        return ApiConstants::TRANSACTION_CODE_REFUND;
    }

    /**
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    public function doTransaction()
    {
        return $this->refund();
    }

    /**
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     */
    protected function refund()
    {
        $transaction = $this->findTransaction();

        if ($transaction->status === ApiConstants::STATUS_CODE_CAPTURE_SUBMITTED) {
            return BraintreeTransaction::void($this->getTransactionIdentifier());
        }

        return BraintreeTransaction::refund(
            $this->getTransactionIdentifier(),
            $this->getAmount()
        );
    }

    /**
     * @return float|null
     */
    protected function getAmount()
    {
        $refundTransfer = $this->transactionMetaTransfer->requireRefund()->getRefund();
        $shipmentExpenseTransfer = $this->getShipmentExpenseTransfer();

        if ($refundTransfer->getAmount() === null) {
            return null;
        }

        $amount = $refundTransfer->getAmount();

        if ($shipmentExpenseTransfer) {
            $amount = $amount - $shipmentExpenseTransfer->getUnitPriceToPayAggregation();
        }

        return $this->moneyFacade->convertIntegerToDecimal($amount);
    }

    /**
     * @return \Braintree\Transaction
     */
    protected function findTransaction()
    {
        return BraintreeTransaction::find($this->getTransactionIdentifier());
    }

    /**
     * @return \Generated\Shared\Transfer\ExpenseTransfer|null
     */
    protected function getShipmentExpenseTransfer(): ?ExpenseTransfer
    {
        foreach ($this->transactionMetaTransfer->getRefund()->getExpenses() as $expenseTransfer) {
            if ($expenseTransfer->getType() === ShipmentConstants::SHIPMENT_EXPENSE_TYPE) {
                return $expenseTransfer;
            }
        }

        return null;
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentBraintreeTransactionStatusLogTransfer|null
     */
    protected function getPaymentBraintreeTransactionStatusLogTransfer(): ?PaymentBraintreeTransactionStatusLogTransfer
    {
        return $this->braintreeRepository
            ->findTransactionRequestLogByIdSalesOrderForShipment($this->transactionMetaTransfer->getIdSalesOrder());
    }
}
