<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction;

use Braintree\Transaction as BraintreeTransaction;
use Spryker\Shared\Shipment\ShipmentConstants;
use SprykerEco\Zed\Braintree\BraintreeConfig;
use SprykerEco\Zed\Braintree\Business\Payment\Method\ApiConstants;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToSalesFacadeInterface;
use SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface;
use SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface;

class CaptureItemsTransaction extends AbstractTransaction
{
    /**
     * @var \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * @var \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface
     */
    protected $braintreeRepository;

    /**
     * @var BraintreeEntityManagerInterface
     */
    protected $braintreeEntityManager;

    /**
     * @var \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToSalesFacadeInterface
     */
    protected $salesFacade;


    /**
     * @param \SprykerEco\Zed\Braintree\BraintreeConfig $config
     * @param \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface $moneyFacade
     * @param \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface $braintreeRepository
     * @param BraintreeEntityManagerInterface $braintreeEntityManager
     * @param \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToSalesFacadeInterface $salesFacade
     */
    public function __construct(
        BraintreeConfig $config,
        BraintreeToMoneyFacadeInterface $moneyFacade,
        BraintreeRepositoryInterface $braintreeRepository,
        BraintreeEntityManagerInterface $braintreeEntityManager,
        BraintreeToSalesFacadeInterface $salesFacade
    ) {
        parent::__construct($config);
        $this->moneyFacade = $moneyFacade;
        $this->braintreeRepository = $braintreeRepository;
        $this->braintreeEntityManager = $braintreeEntityManager;
        $this->salesFacade = $salesFacade;
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
     * @param \Braintree\Result\Successful|\Braintree\Result\Error $response
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    protected function afterTransaction($response)
    {
        if ($this->isTransactionSuccessful($response)) {
            $braintreeTransactionResponseTransfer = $this->getSuccessResponseTransfer($response);
            $this->logApiResponse($braintreeTransactionResponseTransfer, $this->getIdPayment(), $response->transaction->statusHistory);

            $this->braintreeEntityManager->updateIsShipmentPaidValue($this->getIdPayment(), true);

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
        $amount = $this->transactionMetaTransfer->getCaptureAmount();
        $amount = $this->addShipmentAmount($amount);
        $amount = $this->getDecimalAmountValueFromInt($amount);

        return BraintreeTransaction::submitForPartialSettlement(
            $this->getTransactionIdentifier(),
            $amount
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

    /**
     * @param int $amount
     *
     * @return int
     */
    protected function addShipmentAmount(int $amount): int
    {
        $orderTransfer = $this->salesFacade->getOrderByIdSalesOrder($this->transactionMetaTransfer->getIdSalesOrder());
        $braintreePayment = $this->braintreeRepository->findPaymentBraintreeBySalesOrderId($orderTransfer->getIdSalesOrder());

        if (!$braintreePayment || $braintreePayment->getIsShipmentPaid()) {
            return $amount;
        }

        $amount = $amount + $this->getShipmentExpenses($orderTransfer->getExpenses());

        return $amount;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ExpenseTransfer[] $expenseTransfers
     *
     * @return int
     */
    protected function getShipmentExpenses($expenseTransfers): int
    {
        foreach ($expenseTransfers as $expenseTransfer) {
            if ($expenseTransfer->getType() === ShipmentConstants::SHIPMENT_EXPENSE_TYPE) {
                return $expenseTransfer->getUnitPriceToPayAggregation();
            }
        }

        return 0;
    }
}
