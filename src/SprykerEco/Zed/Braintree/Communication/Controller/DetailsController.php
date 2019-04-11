<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method \SprykerEco\Zed\Braintree\Communication\BraintreeCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Braintree\Business\BraintreeFacadeInterface getFacade()
 */
class DetailsController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $idPayment = $this->castId($request->get('id-payment'));
        $paymentEntity = $this->getPaymentEntity($idPayment);
        $requestLogTable = $this->getFactory()->createRequestLogTable($idPayment);
        $statusLogTable = $this->getFactory()->createStatusLogTable($idPayment);

        return [
            'idPayment' => $idPayment,
            'paymentDetails' => $paymentEntity,
            'requestLogTable' => $requestLogTable->render(),
            'statusLogTable' => $statusLogTable->render(),
        ];
    }

    /**
     * @param int $idPayment
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintree
     */
    protected function getPaymentEntity($idPayment)
    {
        $paymentEntity = $this->getQueryContainer()->queryPaymentById($idPayment)->findOne();

        if ($paymentEntity === null) {
            throw new NotFoundHttpException('Payment entity could not be found');
        }

        return $paymentEntity;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function requestLogTableAction(Request $request)
    {
        $idPayment = $this->castId($request->get('id-payment'));
        $requestLogTable = $this->getFactory()->createRequestLogTable($idPayment);

        return $this->jsonResponse($requestLogTable->fetchData());
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function statusLogTableAction(Request $request)
    {
        $idPayment = $this->castId($request->get('id-payment'));
        $statusLogTable = $this->getFactory()->createStatusLogTable($idPayment);

        return $this->jsonResponse($statusLogTable->fetchData());
    }
}
