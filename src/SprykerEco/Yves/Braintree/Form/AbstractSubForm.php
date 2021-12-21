<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Form;

use Braintree\Gateway;
use Generated\Shared\Transfer\BraintreePaymentTransfer;
use Spryker\Shared\Config\Config;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use SprykerEco\Shared\Braintree\BraintreeConfig;
use SprykerEco\Shared\Braintree\BraintreeConstants;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractSubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{
    /**
     * @var string
     */
    protected const CLIENT_TOKEN = 'clientToken';

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
     * @return string
     */
    public function getProviderName()
    {
        return BraintreeConfig::PROVIDER_NAME;
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

        if (Config::hasKey(BraintreeConstants::FAKE_CLIENT_TOKEN)) {
            static::$clientToken = Config::get(BraintreeConstants::FAKE_CLIENT_TOKEN);

            return static::$clientToken;
        }

        $gateway = new Gateway([
            'environment' => Config::get(BraintreeConstants::ENVIRONMENT),
            'merchantId' => Config::get(BraintreeConstants::MERCHANT_ID),
            'publicKey' => Config::get(BraintreeConstants::PUBLIC_KEY),
            'privateKey' => Config::get(BraintreeConstants::PRIVATE_KEY),
        ]);

        static::$clientToken = $gateway->clientToken()->generate();

        return static::$clientToken;
    }
}
