<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\Handler;

use SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface;
use SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface;

abstract class AbstractTransactionHandler
{
    /**
     * @var \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface
     */
    protected $transaction;

    /**
     * @var \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface
     */
    protected $transactionMetaVisitor;

    /**
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\TransactionInterface $transaction
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface $transactionMetaVisitor
     */
    public function __construct(TransactionInterface $transaction, TransactionMetaVisitorInterface $transactionMetaVisitor)
    {
        $this->transaction = $transaction;
        $this->transactionMetaVisitor = $transactionMetaVisitor;
    }
}
