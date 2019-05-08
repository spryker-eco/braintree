<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Service\Braintree\Model\TokenGenerator;

use Braintree\ClientToken;
use Braintree\Configuration;
use SprykerEco\Service\Braintree\BraintreeConfig;

class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * @var \SprykerEco\Service\Braintree\BraintreeConfig
     */
    protected $config;

    /**
     * @var string
     */
    protected static $clientToken;

    /**
     * @param \SprykerEco\Service\Braintree\BraintreeConfig $config
     */
    public function __construct(BraintreeConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function generateToken(): string
    {
        if (static::$clientToken) {
            return static::$clientToken;
        }

        $this->configure();

        static::$clientToken = ClientToken::generate();

        return static::$clientToken;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        Configuration::environment($this->config->getEnvironment());
        Configuration::merchantId($this->config->getMerchantId());
        Configuration::publicKey($this->config->getPublicKey());
        Configuration::privateKey($this->config->getPrivateKey());
    }
}
