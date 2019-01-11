<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Zed\Braintree\Communication\Table\BraintreeTableInterface;
use SprykerEco\Zed\Braintree\Communication\Table\Payments;
use SprykerEco\Zed\Braintree\Communication\Table\RequestLog;
use SprykerEco\Zed\Braintree\Communication\Table\StatusLog;

/**
 * @method \SprykerEco\Zed\Braintree\Persistence\BraintreeQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Braintree\BraintreeConfig getConfig()
 */
class BraintreeCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerEco\Zed\Braintree\Communication\Table\BraintreeTableInterface
     */
    public function createPaymentsTable(): BraintreeTableInterface
    {
        $paymentBraintreeQuery = $this->getQueryContainer()->queryPayments();

        return new Payments($paymentBraintreeQuery);
    }

    /**
     * @param int $idPayment
     *
     * @return \SprykerEco\Zed\Braintree\Communication\Table\BraintreeTableInterface
     */
    public function createRequestLogTable(int $idPayment): BraintreeTableInterface
    {
        $requestLogQuery = $this->getQueryContainer()->queryTransactionRequestLogByPaymentId($idPayment);

        return new RequestLog($requestLogQuery, $idPayment);
    }

    /**
     * @param int $idPayment
     *
     * @return \SprykerEco\Zed\Braintree\Communication\Table\BraintreeTableInterface
     */
    public function createStatusLogTable(int $idPayment): BraintreeTableInterface
    {
        $statusLogQuery = $this->getQueryContainer()->queryTransactionStatusLogByPaymentId($idPayment);

        return new StatusLog($statusLogQuery, $idPayment);
    }
}
