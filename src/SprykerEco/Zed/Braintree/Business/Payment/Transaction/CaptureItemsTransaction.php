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
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentTransactionHandlerInterface;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface;
use SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToSalesFacadeInterface;
use SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface;
use SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface;

class CaptureItemsTransaction extends AbstractTransaction
{
    protected const ATTRIBUTE_KEY_ORDER_ID = 'orderId';

    /**
     * @see \Spryker\Shared\Shipment\ShipmentConfig::SHIPMENT_EXPENSE_TYPE|\Spryker\Shared\Shipment\ShipmentConstants::SHIPMENT_EXPENSE_TYPE
     */
    protected const SHIPMENT_EXPENSE_TYPE = 'SHIPMENT_EXPENSE_TYPE';

    /**
     * @var \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * @var \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface
     */
    protected $braintreeRepository;

    /**
     * @var \SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface
     */
    protected $braintreeEntityManager;

    /**
     * @var \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToSalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentTransactionHandlerInterface
     */
    protected $shipmentTransactionHandler;

    /**
     * @param \SprykerEco\Zed\Braintree\BraintreeConfig $config
     * @param \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToMoneyFacadeInterface $moneyFacade
     * @param \SprykerEco\Zed\Braintree\Persistence\BraintreeRepositoryInterface $braintreeRepository
     * @param \SprykerEco\Zed\Braintree\Persistence\BraintreeEntityManagerInterface $braintreeEntityManager
     * @param \SprykerEco\Zed\Braintree\Dependency\Facade\BraintreeToSalesFacadeInterface $salesFacade
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler\ShipmentTransactionHandlerInterface $shipmentTransactionHandler
     */
    public function __construct(
        BraintreeConfig $config,
        BraintreeToMoneyFacadeInterface $moneyFacade,
        BraintreeRepositoryInterface $braintreeRepository,
        BraintreeEntityManagerInterface $braintreeEntityManager,
        BraintreeToSalesFacadeInterface $salesFacade,
        ShipmentTransactionHandlerInterface $shipmentTransactionHandler
    ) {
        parent::__construct($config);
        $this->moneyFacade = $moneyFacade;
        $this->braintreeRepository = $braintreeRepository;
        $this->braintreeEntityManager = $braintreeEntityManager;
        $this->salesFacade = $salesFacade;
        $this->shipmentTransactionHandler = $shipmentTransactionHandler;
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
    protected function afterTransaction($response): BraintreeTransactionResponseTransfer
    {
        if ($this->isTransactionSuccessful($response)) {
            $braintreeTransactionResponseTransfer = $this->getSuccessResponseTransfer($response);
            $this->logApiResponse($braintreeTransactionResponseTransfer, $this->getIdPayment(), $response->transaction->statusHistory);

            $this->braintreeEntityManager->updateIsShipmentPaidValue($this->getIdPayment(), true);
            $this->braintreeEntityManager->addOrderItemsToTransactionLog(
                $this->getIdPayment(),
                $this->transactionMetaTransfer->getItems(),
                $braintreeTransactionResponseTransfer->getTransactionId()
            );

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
        $this->captureShipmentAmount();

        $amount = $this->getCaptureAmount($this->transactionMetaTransfer->getItems());
        $amount = $this->getDecimalAmountValueFromInt($amount);

        $attributes = [];
        $attributes = $this->addOrderIdToAttributes($attributes);

        return BraintreeTransaction::submitForPartialSettlement(
            $this->getTransactionIdentifier(),
            $amount,
            $attributes
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return int
     */
    protected function getCaptureAmount(iterable $itemTransfers): int
    {
        $amount = 0;

        foreach ($itemTransfers as $itemTransfer) {
            $amount += $itemTransfer->getPriceToPayAggregation();
        }

        return $amount;
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
     * @return void
     */
    protected function captureShipmentAmount(): void
    {
        $orderTransfer = $this->salesFacade->getOrderByIdSalesOrder($this->transactionMetaTransfer->getIdSalesOrder());
        $braintreePayment = $this->braintreeRepository->findPaymentBraintreeBySalesOrderId($orderTransfer->getIdSalesOrder());
        $amount = $this->getShipmentExpenses($orderTransfer->getExpenses());

        if (!$braintreePayment || $braintreePayment->getIsShipmentPaid() || $amount === 0) {
            return;
        }

        $shipmentTransactionMetaTransfer = clone $this->transactionMetaTransfer;
        $shipmentTransactionMetaTransfer->setCaptureShipmentAmount($this->getDecimalAmountValueFromInt($amount));

        $this->shipmentTransactionHandler->captureShipment($shipmentTransactionMetaTransfer);
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    protected function addOrderIdToAttributes(array $attributes): array
    {
        $attributes[static::ATTRIBUTE_KEY_ORDER_ID] = $this->transactionMetaTransfer->getOrderReference();

        return $attributes;
    }

    /**
     * @param \ArrayObject|\Generated\Shared\Transfer\ExpenseTransfer[] $expenseTransfers
     *
     * @return int
     */
    protected function getShipmentExpenses(iterable $expenseTransfers): int
    {
        foreach ($expenseTransfers as $expenseTransfer) {
            if ($expenseTransfer->getType() === static::SHIPMENT_EXPENSE_TYPE) {
                return $expenseTransfer->getUnitPriceToPayAggregation();
            }
        }

        return 0;
    }
}
