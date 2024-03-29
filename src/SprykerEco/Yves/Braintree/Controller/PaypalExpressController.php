<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Controller;

use Spryker\Yves\Kernel\Controller\AbstractController;
use SprykerEco\Yves\Braintree\Form\CheckoutShipmentForm;
use SprykerShop\Yves\CheckoutPage\Plugin\Provider\CheckoutPageControllerProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \SprykerEco\Yves\Braintree\BraintreeFactory getFactory()
 * @method \Spryker\Yves\Kernel\Application getApplication()
 */
class PaypalExpressController extends AbstractController
{
    /**
     * @var string
     */
    public const TRANSLATION_INVALID_SHIPMENT_METHOD = 'checkout.pre.check.shipment.failed';

    /**
     * @var bool
     */
    public const IS_PAYPAL_ENABLED = true;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function successAction(Request $request): Response
    {
        if (!static::IS_PAYPAL_ENABLED) {
            echo 'The payment method PayPal Express is not an officially approved integration and must not be used without prior agreement with either Braintree and/or Spryker.';

            return $this->jsonResponse([
                'redirectUrl' => $this->getApplication()->path(CheckoutPageControllerProvider::CHECKOUT_SUMMARY),
            ]);
        }

        $payload = $this->getFactory()->getUtilEncodingService()->decodeJson((string)$request->getContent(), true);

        $this->getFactory()->createResponseProcessor()->processSuccessResponse($payload);

        return $this->jsonResponse([
            'redirectUrl' => $this->getApplication()->path(CheckoutPageControllerProvider::CHECKOUT_SUMMARY),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addShipmentAction(Request $request): Response
    {
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();

        $form = $this->getFactory()->getFormFactory()->create(
            CheckoutShipmentForm::class,
            $this->getFactory()->createBraintreePaypalExpressShipmentFormDataProvider()->getData($quoteTransfer),
            $this->getFactory()->createBraintreePaypalExpressShipmentFormDataProvider()->getOptions($quoteTransfer),
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getFactory()->createQuoteExpander()->expandQuoteWithShipmentMethod(
                $request,
                $form->getData()->getShipment()->getShipmentSelection(),
            );

            return $this->redirectResponseInternal(CheckoutPageControllerProvider::CHECKOUT_SUMMARY);
        }

        $this->getFactory()->getMessengerClient()->addErrorMessage(static::TRANSLATION_INVALID_SHIPMENT_METHOD);

        return $this->redirectResponseInternal(CheckoutPageControllerProvider::CHECKOUT_SUMMARY);
    }
}
