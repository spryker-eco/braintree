<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Form;

use Generated\Shared\Transfer\BraintreePaymentTransfer;
use Spryker\Shared\Config\Config;
use SprykerEco\Shared\Braintree\BraintreeConfig;
use SprykerEco\Shared\Braintree\BraintreeConstants;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreditCardSubForm extends AbstractSubForm
{
    public const PAYMENT_METHOD = 'credit-card';

    public const IS_3D_SECURE = 'is3dSecure';
    public const AMOUNT = 'amount';
    public const EMAIL = 'email';

    public const BILLING_ADDRESS = 'billingAddress';
    public const BILLING_ADDRESS_GIVEN_NAME = 'givenName';
    public const BILLING_ADDRESS_SURNAME = 'surname';
    public const BILLING_ADDRESS_PHONE_NUMBER = 'phoneNumber';
    public const BILLING_ADDRESS_STREET_ADDRESS = 'streetAddress';
    public const BILLING_ADDRESS_EXTENDED_ADDRESS = 'extendedAddress';
    public const BILLING_ADDRESS_LOCALITY = 'locality';
    public const BILLING_ADDRESS_REGION = 'region';
    public const BILLING_ADDRESS_POSTAL_CODE = 'postalCode';
    public const BILLING_ADDRESS_COUNTRY_CODE = 'countryCodeAlpha2';

    /**
     * @return string
     */
    public function getName()
    {
        return BraintreeConfig::PAYMENT_METHOD_CREDIT_CARD;
    }

    /**
     * @return string
     */
    public function getPropertyPath()
    {
        return BraintreeConfig::PAYMENT_METHOD_CREDIT_CARD;
    }

    /**
     * @return string
     */
    public function getTemplatePath()
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

    /**
     * @param \Symfony\Component\Form\FormView $view The view
     * @param \Symfony\Component\Form\FormInterface $form The form
     * @param array $options The options
     *
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars[static::CLIENT_TOKEN] = $this->generateClientToken();
        $view->vars[static::IS_3D_SECURE] = (string)Config::get(BraintreeConstants::IS_3D_SECURE);

        /** @var \Generated\Shared\Transfer\QuoteTransfer $quote */
        $quote = $form->getParent()->getViewData();

        $view->vars[static::EMAIL] = $quote->getCustomer()->getEmail();
        $view->vars[static::AMOUNT] = $quote->getTotals()->getGrandTotal();
        $view->vars[static::BILLING_ADDRESS] = [
            static::BILLING_ADDRESS_GIVEN_NAME => $quote->getBillingAddress()->getFirstName(),
            static::BILLING_ADDRESS_SURNAME => $quote->getBillingAddress()->getLastName(),
            static::BILLING_ADDRESS_PHONE_NUMBER => $quote->getBillingAddress()->getPhone(),
            static::BILLING_ADDRESS_STREET_ADDRESS => $quote->getBillingAddress()->getAddress1(),
            static::BILLING_ADDRESS_EXTENDED_ADDRESS => $quote->getBillingAddress()->getAddress2(),
            static::BILLING_ADDRESS_LOCALITY => $quote->getBillingAddress()->getCountry() ? $quote->getBillingAddress()->getCountry()->getName() : '',
            static::BILLING_ADDRESS_REGION => $quote->getBillingAddress()->getRegion(),
            static::BILLING_ADDRESS_POSTAL_CODE => $quote->getBillingAddress()->getZipCode(),
            static::BILLING_ADDRESS_COUNTRY_CODE => $quote->getBillingAddress()->getCountry() ? $quote->getBillingAddress()->getCountry()->getIso2Code() : '',
        ];
    }
}
