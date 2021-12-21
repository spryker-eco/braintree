<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Method;

class ApiConstants
{
    /**
     * @var string
     */
    public const SALE = 'sale';

    /**
     * @var string
     */
    public const CREDIT = 'credit';

    /**
     * @var string
     */
    public const TRANSACTION_CODE_AUTHORIZE = 'authorize';

    /**
     * @var string
     */
    public const TRANSACTION_CODE_CAPTURE = 'capture';

    /**
     * @var string
     */
    public const TRANSACTION_CODE_REVERSAL = 'reversal';

    /**
     * @var string
     */
    public const TRANSACTION_CODE_REFUND = 'refund';

    /**
     * @var string
     */
    public const STATUS_CODE_PRE_CHECK = 'pre check';

    /**
     * @var string
     */
    public const STATUS_CODE_AUTHORIZE = 'authorized';

    /**
     * @var string
     */
    public const STATUS_CODE_CAPTURE = 'settling'; // Braintree\Transaction::SETTLEMENT_CONFIRMED

    /**
     * @var string
     */
    public const STATUS_CODE_CAPTURE_SUBMITTED = 'submitted_for_settlement';

    /**
     * @var string
     */
    public const STATUS_CODE_REVERSAL = 'voided';

    /**
     * @var string
     */
    public const STATUS_CODE_REFUND = 'settling';

    /**
     * @var string
     */
    public const PAYMENT_CODE_AUTHORIZE_SUCCESS = '1000';

    /**
     * @var string
     */
    public const STATUS_REASON_CODE_SUCCESS = '1';
}
