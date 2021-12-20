<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor;

use Generated\Shared\Transfer\TransactionMetaTransfer;

class TransactionMetaVisitorComposite implements TransactionMetaVisitorInterface
{
    /**
     * @var array<\SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface>
     */
    protected $visitor = [];

    /**
     * @param \SprykerEco\Zed\Braintree\Business\Payment\Transaction\MetaVisitor\TransactionMetaVisitorInterface $visitor
     *
     * @return $this
     */
    public function addVisitor(TransactionMetaVisitorInterface $visitor)
    {
        $this->visitor[] = $visitor;

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\TransactionMetaTransfer $transactionMetaTransfer
     *
     * @return void
     */
    public function visit(TransactionMetaTransfer $transactionMetaTransfer)
    {
        foreach ($this->visitor as $visitor) {
            $visitor->visit($transactionMetaTransfer);
        }
    }
}
