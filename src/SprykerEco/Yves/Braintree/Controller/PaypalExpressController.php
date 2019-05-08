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
 */
class PaypalExpressController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function successAction(Request $request): Response
    {
        $payload = $this->getFactory()->getUtilEncodingService()->decodeJson($request->getContent(), true);

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
        $idShipmentMethod = $request->get(CheckoutShipmentForm::FORM_NAME)[CheckoutShipmentForm::FIELD_ID_SHIPMENT_METHOD];

        $this->getFactory()->createQuoteExpander()->expandQuoteWithShipmentMethod($request, $idShipmentMethod);

        return $this->redirectResponseInternal(CheckoutPageControllerProvider::CHECKOUT_SUMMARY);
    }
}