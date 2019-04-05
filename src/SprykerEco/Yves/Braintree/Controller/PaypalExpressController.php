<?php

namespace SprykerEco\Yves\Braintree\Controller;

use Spryker\Yves\Kernel\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \SprykerEco\Yves\Braintree\BraintreeFactory getFactory()
 */
class PaypalExpressController extends AbstractController
{
    public function successAction(Request $request)
    {
        $payload = $this->getFactory()->getUtilEncodingService()->decodeJson($request->getContent(), true);

        $this->getFactory()->createResponseProcessor()->processSuccessResponse($payload);

        return $this->jsonResponse([
            'redirectUrl' => 'http://www.de.suite-nonsplit.local/checkout/summary'
        ]);
    }
}