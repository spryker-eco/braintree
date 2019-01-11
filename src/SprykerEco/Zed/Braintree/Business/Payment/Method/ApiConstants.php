<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Braintree\Business\Payment\Method;

class ApiConstants
{
    public const SALE = 'sale';
    public const CREDIT = 'credit';

    public const TRANSACTION_CODE_AUTHORIZE = 'authorize';
    public const TRANSACTION_CODE_CAPTURE = 'capture';
    public const TRANSACTION_CODE_REVERSAL = 'reversal';
    public const TRANSACTION_CODE_REFUND = 'refund';

    public const STATUS_CODE_PRE_CHECK = 'pre check';
    public const STATUS_CODE_AUTHORIZE = 'authorized';
    public const STATUS_CODE_CAPTURE = 'settling'; // Braintree\Transaction::SETTLEMENT_CONFIRMED
    public const STATUS_CODE_CAPTURE_SUBMITTED = 'submitted_for_settlement';
    public const STATUS_CODE_REVERSAL = 'voided';
    public const STATUS_CODE_REFUND = 'settling';

    public const PAYMENT_CODE_AUTHORIZE_SUCCESS = '1000';
    public const STATUS_REASON_CODE_SUCCESS = '1';
}
