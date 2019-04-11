<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Controller;

use Spryker\Yves\Kernel\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
    public function successAction(Request $request)
    {
        $payload = $this->getFactory()->getUtilEncodingService()->decodeJson($request->getContent(), true);

        $this->getFactory()->createResponseProcessor()->processSuccessResponse($payload);

        //TODO: Update route
        return $this->jsonResponse([
            'redirectUrl' => 'http://www.de.suite-nonsplit.local/checkout/summary',
        ]);
    }
}
