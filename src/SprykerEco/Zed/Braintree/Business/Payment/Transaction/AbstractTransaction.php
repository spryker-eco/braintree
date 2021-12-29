<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction;

use Braintree\Configuration;
use Generated\Shared\Transfer\BraintreeTransactionResponseTransfer;
use Generated\Shared\Transfer\TransactionMetaTransfer;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog;
use Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionStatusLog;
use SprykerEco\Zed\Braintree\BraintreeConfig;

abstract class AbstractTransaction implements TransactionInterface
{
    /**
     * @var \Generated\Shared\Transfer\TransactionMetaTransfer
     */
    protected $transactionMetaTransfer;

    /**
     * @var \SprykerEco\Zed\Braintree\BraintreeConfig
     */
    protected $config;

    /**
     * @param \SprykerEco\Zed\Braintree\BraintreeConfig $config
     */
    public function __construct(BraintreeConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    public function executeTransaction(TransactionMetaTransfer $transactionMetaTransfer)
    {
        $this->initializeBraintree();

        $this->transactionMetaTransfer = $transactionMetaTransfer;

        $this->beforeTransaction();
        $response = $this->doTransaction();
        $transactionResponse = $this->afterTransaction($response);

        return $transactionResponse;
    }

    /**
     * @return string
     */
    protected function getTransactionIdentifier()
    {
        return $this->transactionMetaTransfer->requireTransactionIdentifier()->getTransactionIdentifier();
    }

    /**
     * @return int
     */
    protected function getIdPayment()
    {
        return $this->transactionMetaTransfer->getIdPaymentOrFail();
    }

    /**
     * @return void
     */
    protected function beforeTransaction()
    {
        $this->logApiRequest(
            $this->getTransactionIdentifier(),
            $this->getTransactionType(),
            $this->getTransactionCode(),
            $this->getIdPayment(),
        );
    }

    /**
     * @return string
     */
    abstract protected function getTransactionType();

    /**
     * @return string
     */
    abstract protected function getTransactionCode();

    /**
     * @return \Braintree\Result\Successful|\Braintree\Result\Error|\Braintree\Transaction
     */
    abstract protected function doTransaction();

    /**
     * @param \Braintree\Result\Successful|\Braintree\Result\Error|\Braintree\Transaction $response
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    protected function afterTransaction($response)
    {
        if ($this->isTransactionSuccessful($response)) {
            $braintreeTransactionResponseTransfer = $this->getSuccessResponseTransfer($response);
            $transaction = $response->transaction;
            $statusHistory = $transaction->__get('statusHistory');

            $this->logApiResponse($braintreeTransactionResponseTransfer, $this->getIdPayment(), $statusHistory);

            return $braintreeTransactionResponseTransfer;
        }

        $braintreeTransactionResponseTransfer = $this->getErrorResponseTransfer($response);
        $this->logApiResponse($braintreeTransactionResponseTransfer, $this->getIdPayment());

        return $braintreeTransactionResponseTransfer;
    }

    /**
     * @param \Braintree\Result\Successful|\Braintree\Result\Error|\Braintree\Transaction $response
     *
     * @return bool
     */
    protected function isTransactionSuccessful($response)
    {
        return $response->success;
    }

    /**
     * @param \Braintree\Result\Successful|\Braintree\Result\Error $response
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    protected function getSuccessResponseTransfer($response)
    {
        $transaction = $response->transaction;
        $braintreeTransactionResponseTransfer = $this->getResponseTransfer()
            ->setIsSuccess(true)
            ->setTransactionId($transaction->__get('id'))
            ->setCode($transaction->__get('processorResponseCode'))
            ->setMessage($transaction->__get('processorResponseText'))
            ->setProcessingTimestamp($transaction->__get('createdAt')->getTimestamp())
            ->setTransactionStatus($transaction->__get('status'))
            ->setTransactionType($transaction->__get('type'))
            ->setTransactionAmount($transaction->__get('amount'))
            ->setMerchantAccount($transaction->__get('merchantAccountId'));

        return $braintreeTransactionResponseTransfer;
    }

    /**
     * @param \Braintree\Result\Successful|\Braintree\Result\Error $response
     *
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    protected function getErrorResponseTransfer($response)
    {
        $braintreeTransactionResponseTransfer = $this->getResponseTransfer()
            ->setIsSuccess(false)
            ->setMessage($response->__get('message'));

        return $braintreeTransactionResponseTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer
     */
    protected function getResponseTransfer()
    {
        $braintreeTransactionResponseTransfer = new BraintreeTransactionResponseTransfer();
        $braintreeTransactionResponseTransfer
            ->setTransactionId($this->getTransactionIdentifier())
            ->setTransactionCode($this->getTransactionCode());

        return $braintreeTransactionResponseTransfer;
    }

    /**
     * @param string $transactionId
     * @param string $transactionType
     * @param string $transactionCode
     * @param int $idPayment
     *
     * @return \Orm\Zed\Braintree\Persistence\SpyPaymentBraintreeTransactionRequestLog
     */
    protected function logApiRequest($transactionId, $transactionType, $transactionCode, $idPayment)
    {
        $logEntity = new SpyPaymentBraintreeTransactionRequestLog();
        $logEntity
            ->setTransactionId($transactionId)
            ->setTransactionType($transactionType)
            ->setTransactionCode($transactionCode)
            ->setFkPaymentBraintree($idPayment);
        $logEntity->save();

        return $logEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\BraintreeTransactionResponseTransfer $responseTransfer
     * @param int $idPayment
     * @param array $logs
     *
     * @return void
     */
    protected function logApiResponse(BraintreeTransactionResponseTransfer $responseTransfer, $idPayment, array $logs = [])
    {
        if (count($logs) > 0) {
            $log = array_pop($logs);
            $responseTransfer
                ->setTransactionStatus($log->status)
                ->setTransactionAmount($log->amount)
                ->setProcessingTimestamp($log->timestamp->getTimestamp());
        }

        $logEntity = new SpyPaymentBraintreeTransactionStatusLog();
        $logEntity->fromArray($responseTransfer->toArray());
        $logEntity->setFkPaymentBraintree($idPayment);
        $logEntity->save();
    }

    /**
     * @return void
     */
    protected function initializeBraintree()
    {
        Configuration::environment($this->config->getEnvironment());
        Configuration::merchantId($this->config->getMerchantId());
        Configuration::publicKey($this->config->getPublicKey());
        Configuration::privateKey($this->config->getPrivateKey());
    }
}
