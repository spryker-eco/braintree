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
use Symfony\Component\String\UnicodeString;

class CreditCardSubForm extends AbstractSubForm
{
    /**
     * @var string
     */
    public const PAYMENT_METHOD = 'credit-card';

    /**
     * @var string
     */
    public const IS_3D_SECURE = 'is3dSecure';

    /**
     * @var string
     */
    public const AMOUNT = 'amount';

    /**
     * @var string
     */
    public const EMAIL = 'email';

    /**
     * @var string
     */
    public const BILLING_ADDRESS = 'billingAddress';

    /**
     * @var string
     */
    public const BILLING_ADDRESS_GIVEN_NAME = 'givenName';

    /**
     * @var string
     */
    public const BILLING_ADDRESS_SURNAME = 'surname';

    /**
     * @var string
     */
    public const BILLING_ADDRESS_PHONE_NUMBER = 'phoneNumber';

    /**
     * @var string
     */
    public const BILLING_ADDRESS_STREET_ADDRESS = 'streetAddress';

    /**
     * @var string
     */
    public const BILLING_ADDRESS_EXTENDED_ADDRESS = 'extendedAddress';

    /**
     * @var string
     */
    public const BILLING_ADDRESS_LOCALITY = 'locality';

    /**
     * @var string
     */
    public const BILLING_ADDRESS_REGION = 'region';

    /**
     * @var string
     */
    public const BILLING_ADDRESS_POSTAL_CODE = 'postalCode';

    /**
     * @var string
     */
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

        $parentForm = $form->getParent();
        if ($parentForm instanceof FormInterface) {
            /** @var \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer */
            $quoteTransfer = $parentForm->getViewData();

            $billingAddressTransfer = $quoteTransfer->getBillingAddressOrFail();

            $view->vars[static::EMAIL] = $quoteTransfer->getCustomerOrFail()->getEmail();
            $view->vars[static::AMOUNT] = $quoteTransfer->getTotals()->getGrandTotal();
            $view->vars[static::BILLING_ADDRESS] = [
                static::BILLING_ADDRESS_GIVEN_NAME => $this->convertToGermanAsciiFormat($billingAddressTransfer->getFirstNameOrFail()),
                static::BILLING_ADDRESS_SURNAME => $this->convertToGermanAsciiFormat($billingAddressTransfer->getLastNameOrFail()),
                static::BILLING_ADDRESS_PHONE_NUMBER => $billingAddressTransfer->getPhone(),
                static::BILLING_ADDRESS_STREET_ADDRESS => $billingAddressTransfer->getAddress1(),
                static::BILLING_ADDRESS_EXTENDED_ADDRESS => $billingAddressTransfer->getAddress2(),
                static::BILLING_ADDRESS_LOCALITY => $billingAddressTransfer->getCountry() ? $billingAddressTransfer->getCountry()->getName() : '',
                static::BILLING_ADDRESS_REGION => $billingAddressTransfer->getRegion(),
                static::BILLING_ADDRESS_POSTAL_CODE => $billingAddressTransfer->getZipCode(),
                static::BILLING_ADDRESS_COUNTRY_CODE => $billingAddressTransfer->getCountry() ? $billingAddressTransfer->getCountry()->getIso2Code() : '',
            ];
        }
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function convertToGermanAsciiFormat(string $string): string
    {
        return (new UnicodeString($string))
            ->ascii(['de-ascii'])
            ->toString();
    }
}
