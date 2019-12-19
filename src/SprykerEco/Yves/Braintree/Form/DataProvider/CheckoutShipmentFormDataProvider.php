<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Form\DataProvider;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentMethodsCollectionTransfer;
use Generated\Shared\Transfer\ShipmentMethodsTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Spryker\Shared\Kernel\Store;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToGlossaryClientInterface;
use SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToShipmentClientInterface;
use SprykerEco\Yves\Braintree\Form\CheckoutShipmentForm;

class CheckoutShipmentFormDataProvider implements StepEngineFormDataProviderInterface
{
    public const FIELD_ID_SHIPMENT_METHOD = 'idShipmentMethod';

    public const TRANSLATION_KEY_DELIVERY_TIME = 'page.checkout.shipping.delivery_time';
    public const TRANSLATION_KEY_DAY = 'page.checkout.shipping.day';
    public const TRANSLATION_KEY_DAYS = 'page.checkout.shipping.days';

    protected const SECONDS_IN_A_DAY = 86400;

    /**
     * @var \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToShipmentClientInterface
     */
    protected $shipmentClient;

    /**
     * @var \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToGlossaryClientInterface
     */
    protected $glossaryClient;

    /**
     * @var \Spryker\Shared\Kernel\Store
     */
    protected $store;

    /**
     * @var \Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface
     */
    protected $moneyPlugin;

    /**
     * @param \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToShipmentClientInterface $shipmentClient
     * @param \SprykerEco\Yves\Braintree\Dependency\Client\BraintreeToGlossaryClientInterface $glossaryClient
     * @param \Spryker\Shared\Kernel\Store $store
     * @param \Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface $moneyPlugin
     */
    public function __construct(
        BraintreeToShipmentClientInterface $shipmentClient,
        BraintreeToGlossaryClientInterface $glossaryClient,
        Store $store,
        MoneyPluginInterface $moneyPlugin
    ) {
        $this->shipmentClient = $shipmentClient;
        $this->glossaryClient = $glossaryClient;
        $this->store = $store;
        $this->moneyPlugin = $moneyPlugin;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getData(AbstractTransfer $quoteTransfer): QuoteTransfer
    {
        if ($quoteTransfer->getShipment() === null) {
            $shipmentTransfer = new ShipmentTransfer();
            $quoteTransfer->setShipment($shipmentTransfer);
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function getOptions(AbstractTransfer $quoteTransfer): array
    {
        return [
            CheckoutShipmentForm::OPTION_SHIPMENT_METHODS => $this->createAvailableShipmentChoiceList($quoteTransfer),
            CheckoutShipmentForm::OPTION_ID_SELECTED_SHIPMENT_METHOD => $this->getSelectedShipmentMethodId($quoteTransfer),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    protected function createAvailableShipmentChoiceList(QuoteTransfer $quoteTransfer): array
    {
        $shipmentMethods = [];

        $shipmentMethodsTransfer = $this->getAvailableShipmentMethods($quoteTransfer);
        foreach ($shipmentMethodsTransfer->getShipmentMethods() as $shipmentMethodsTransfer) {
            foreach ($shipmentMethodsTransfer->getMethods() as $shipmentMethodTransfer) {
                if (!isset($shipmentMethods[$shipmentMethodTransfer->getCarrierName()])) {
                    $shipmentMethods[$shipmentMethodTransfer->getCarrierName()] = [];
                }
                $description = $this->getShipmentDescription(
                    $shipmentMethodTransfer
                );
                $shipmentMethods[$shipmentMethodTransfer->getCarrierName()][$description] = $shipmentMethodTransfer->getIdShipmentMethod();
            }
        }

        return $shipmentMethods;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ShipmentMethodsCollectionTransfer
     */
    protected function getAvailableShipmentMethods(QuoteTransfer $quoteTransfer): ShipmentMethodsCollectionTransfer
    {
        return $this->shipmentClient->getAvailableMethods($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer $shipmentMethodTransfer
     *
     * @return string
     */
    protected function getShipmentDescription(ShipmentMethodTransfer $shipmentMethodTransfer): string
    {
        $shipmentDescription = $this->translate($shipmentMethodTransfer->getName());

        $shipmentDescription = $this->appendDeliveryTime($shipmentMethodTransfer, $shipmentDescription);
        $shipmentDescription = $this->appendShipmentPrice($shipmentMethodTransfer, $shipmentDescription);

        return $shipmentDescription;
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer $shipmentMethodTransfer
     * @param string $shipmentDescription
     *
     * @return string
     */
    protected function appendDeliveryTime(ShipmentMethodTransfer $shipmentMethodTransfer, $shipmentDescription): string
    {
        $deliveryTime = $this->getDeliveryTime($shipmentMethodTransfer);

        if ($deliveryTime !== 0) {
            $shipmentDescription = sprintf(
                '%s (%s %d %s)',
                $shipmentDescription,
                $this->translate(static::TRANSLATION_KEY_DELIVERY_TIME),
                $deliveryTime,
                $this->getTranslatedDayName($deliveryTime)
            );
        }

        return $shipmentDescription;
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer $shipmentMethodTransfer
     * @param string $shipmentDescription
     *
     * @return string
     */
    protected function appendShipmentPrice(ShipmentMethodTransfer $shipmentMethodTransfer, $shipmentDescription): string
    {
        $shipmentPrice = $this->getFormattedShipmentPrice($shipmentMethodTransfer);
        $shipmentDescription .= ': ' . $shipmentPrice;

        return $shipmentDescription;
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer $method
     *
     * @return int
     */
    protected function getDeliveryTime(ShipmentMethodTransfer $method): int
    {
        if (!$method->getDeliveryTime()) {
            return 0;
        }

        return (int)ceil($method->getDeliveryTime() / static::SECONDS_IN_A_DAY);
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer $shipmentMethodTransfer
     *
     * @return string
     */
    protected function getFormattedShipmentPrice(ShipmentMethodTransfer $shipmentMethodTransfer): string
    {
        $moneyTransfer = $this->moneyPlugin
            ->fromInteger($shipmentMethodTransfer->getStoreCurrencyPrice());

        return $this->moneyPlugin->formatWithSymbol($moneyTransfer);
    }

    /**
     * @param string $translationKey
     *
     * @return string
     */
    protected function translate($translationKey): string
    {
        return $this->glossaryClient->translate($translationKey, $this->store->getCurrentLocale());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return int|null
     */
    protected function getSelectedShipmentMethodId(QuoteTransfer $quoteTransfer): ?int
    {
        $shipment = $quoteTransfer->getShipment();

        if (!$shipment) {
            return null;
        }

        return $shipment->getMethod() ? $shipment->getMethod()->getIdShipmentMethod() : null;
    }

    /**
     * @param int $deliveryTime
     *
     * @return string
     */
    protected function getTranslatedDayName(int $deliveryTime): string
    {
        if ($deliveryTime === 1) {
            return $this->translate(static::TRANSLATION_KEY_DAY);
        }

        return $this->translate(static::TRANSLATION_KEY_DAYS);
    }
}
