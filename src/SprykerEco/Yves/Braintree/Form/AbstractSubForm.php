<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Braintree\Form;

use Braintree\ClientToken;
use Braintree\Configuration;
use Generated\Shared\Transfer\BraintreePaymentTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use SprykerEco\Shared\Braintree\BraintreeConstants;
use Spryker\Shared\Config\Config;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractSubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{

    const CLIENT_TOKEN = 'clientToken';

    /**
     * @var string
     */
    protected static $clientToken;

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BraintreePaymentTransfer::class,
            SubFormInterface::OPTIONS_FIELD_NAME => [],
        ]);
    }

    /**
     * @deprecated Use `configureOptions()` instead.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return BraintreeConstants::PROVIDER_NAME;
    }

    /**
     * Generate client token and store it in static class attribute to ensure
     * we do not invoke the API twice here for multiple sub forms.
     *
     * @return string
     */
    protected function generateClientToken()
    {
        if (static::$clientToken) {
            return static::$clientToken;
        }

        $environment = Config::get(BraintreeConstants::ENVIRONMENT);
        $merchantId = Config::get(BraintreeConstants::MERCHANT_ID);
        $publicKey = Config::get(BraintreeConstants::PUBLIC_KEY);
        $privateKey = Config::get(BraintreeConstants::PRIVATE_KEY);
        Configuration::environment($environment);
        Configuration::merchantId($merchantId);
        Configuration::publicKey($publicKey);
        Configuration::privateKey($privateKey);

        static::$clientToken = ClientToken::generate();

        return static::$clientToken;
    }

}
