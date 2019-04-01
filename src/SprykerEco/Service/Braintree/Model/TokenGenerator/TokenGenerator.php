<?php

namespace SprykerEco\Service\Braintree\Model\TokenGenerator;

use Braintree\ClientToken;
use Braintree\Configuration;
use SprykerEco\Service\Braintree\BraintreeConfig;

class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * @var BraintreeConfig
     */
    protected $config;

    /**
     * @var
     */
    protected static $clientToken;

    /**
     * @param BraintreeConfig $config
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

        Configuration::environment($this->config->getEnvironment());
        Configuration::merchantId($this->config->getMerchantId());
        Configuration::publicKey($this->config->getPublicKey());
        Configuration::privateKey($this->config->getPrivateKey());

        static::$clientToken = ClientToken::generate();

        return static::$clientToken;
    }
}