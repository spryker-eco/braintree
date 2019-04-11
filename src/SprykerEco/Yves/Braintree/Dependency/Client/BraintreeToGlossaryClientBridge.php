<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Dependency\Client;

class BraintreeToGlossaryClientBridge implements BraintreeToGlossaryClientInterface
{
    /**
     * @var \Spryker\Client\Glossary\GlossaryClientInterface
     */
    protected $glossaryClient;

    /**
     * @param \Spryker\Client\Glossary\GlossaryClientInterface $glossaryClient
     */
    public function __construct($glossaryClient)
    {
        $this->glossaryClient = $glossaryClient;
    }

    /**
     * @param string $id
     * @param string $localeName
     * @param array $parameters
     *
     * @return string
     */
    public function translate($id, $localeName, array $parameters = [])
    {
        return $this->glossaryClient->translate($id, $localeName, $parameters);
    }
}
