<?php

namespace SprykerEco\Service\Braintree;

interface BraintreeServiceInterface
{
    /**
     * @return string
     */
    public function generateToken(): string;
}