<?php

namespace SprykerEco\Service\Braintree\Model\TokenGenerator;

interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generateToken(): string;
}