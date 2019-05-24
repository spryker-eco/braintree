<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Form;

use Generated\Shared\Transfer\BraintreePaymentTransfer;
use SprykerEco\Shared\Braintree\BraintreeConfig;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayPalExpressSubForm extends AbstractSubForm
{
    public const PAYMENT_METHOD = 'pay-pal-express';

    /**
     * @return string
     */
    public function getName(): string
    {
        return BraintreeConfig::PAYMENT_METHOD_PAY_PAL_EXPRESS;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return BraintreeConfig::PAYMENT_METHOD_PAY_PAL_EXPRESS;
    }

    /**
     * @return string
     */
    public function getTemplatePath(): string
    {
        return BraintreeConfig::PROVIDER_NAME . '/' . static::PAYMENT_METHOD;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BraintreePaymentTransfer::class,
        ])->setRequired(static::OPTIONS_FIELD_NAME);
    }
}
