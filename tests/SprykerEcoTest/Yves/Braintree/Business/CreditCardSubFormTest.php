<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Yves\Braintree\Business;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\AddressBuilder;
use Generated\Shared\DataBuilder\CustomerBuilder;
use Generated\Shared\Transfer\BraintreePaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use SprykerEco\Shared\Braintree\BraintreeConstants;
use SprykerEco\Yves\Braintree\Form\CreditCardSubForm;
use SprykerEcoTest\Yves\Braintree\Form\FakeParentForm;
use Symfony\Component\Form\FormView;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Yves
 * @group Braintree
 * @group Business
 * @group CreditCardSubFormTest
 * Add your own group annotations below this line
 */
class CreditCardSubFormTest extends Unit
{
    /**
     * @var \SprykerEcoTest\Yves\Braintree\BraintreeBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->setConfig(BraintreeConstants::FAKE_CLIENT_TOKEN, 'FAKE_CLIENT_TOKEN');
        $this->tester->setConfig(BraintreeConstants::IS_3D_SECURE, false);
    }

    /**
     * @return void
     */
    public function testBuildCreditCardSubFormForCustomerWithoutNonAsciiChars(): void
    {
        $addressTransfer = (new AddressBuilder())->build();

        $quoteTransfer = (new QuoteTransfer())
            ->setCustomer((new CustomerBuilder())->build())
            ->setTotals((new TotalsTransfer())->setGrandTotal(666))
            ->setBillingAddress($addressTransfer);

        $view = $this->buildCreditCardSubForm($quoteTransfer);

        $this->assertSame(
            $addressTransfer->getFirstName(),
            $view->vars[CreditCardSubForm::BILLING_ADDRESS][CreditCardSubForm::BILLING_ADDRESS_GIVEN_NAME]
        );
        $this->assertSame(
            $addressTransfer->getLastName(),
            $view->vars[CreditCardSubForm::BILLING_ADDRESS][CreditCardSubForm::BILLING_ADDRESS_SURNAME]
        );
    }

    /**
     * @return void
     */
    public function testBuildCreditCardSubFormForCustomerWithNonAsciiChars(): void
    {
        $addressTransfer = (new AddressBuilder())->build()
            ->setFirstName('Soniüa')
            ->setLastName('WΔagner');

        $quoteTransfer = (new QuoteTransfer())
            ->setCustomer((new CustomerBuilder())->build())
            ->setTotals((new TotalsTransfer())->setGrandTotal(666))
            ->setBillingAddress($addressTransfer);

        $view = $this->buildCreditCardSubForm($quoteTransfer);

        $this->assertSame(
            'Soniuea',
            $view->vars[CreditCardSubForm::BILLING_ADDRESS][CreditCardSubForm::BILLING_ADDRESS_GIVEN_NAME]
        );
        $this->assertSame(
            'WDagner',
            $view->vars[CreditCardSubForm::BILLING_ADDRESS][CreditCardSubForm::BILLING_ADDRESS_SURNAME]
        );
    }

    /**
     * @return void
     */
    public function testBuildCreditCardSubFormConvertCustomerGermanNames(): void
    {
        $addressTransfer = (new AddressBuilder())->build()
            ->setFirstName('Herr')
            ->setLastName('Müßig');

        $quoteTransfer = (new QuoteTransfer())
            ->setCustomer((new CustomerBuilder())->build())
            ->setTotals((new TotalsTransfer())->setGrandTotal(666))
            ->setBillingAddress($addressTransfer);

        $view = $this->buildCreditCardSubForm($quoteTransfer);

        $this->assertSame(
            'Herr',
            $view->vars[CreditCardSubForm::BILLING_ADDRESS][CreditCardSubForm::BILLING_ADDRESS_GIVEN_NAME]
        );
        $this->assertSame(
            'Muessig',
            $view->vars[CreditCardSubForm::BILLING_ADDRESS][CreditCardSubForm::BILLING_ADDRESS_SURNAME]
        );
    }

    /**
     * @return void
     */
    public function testBuildCreditCardSubFormForCustomerWithFullNonAsciiChars(): void
    {
        $addressTransfer = (new AddressBuilder())->build()
            ->setFirstName('ΚοΒü')
            ->setLastName('ΑƒƒΔΨ');

        $quoteTransfer = (new QuoteTransfer())
            ->setCustomer((new CustomerBuilder())->build())
            ->setTotals((new TotalsTransfer())->setGrandTotal(666))
            ->setBillingAddress($addressTransfer);

        $view = $this->buildCreditCardSubForm($quoteTransfer);

        $this->assertSame(
            'KoBue',
            $view->vars[CreditCardSubForm::BILLING_ADDRESS][CreditCardSubForm::BILLING_ADDRESS_GIVEN_NAME]
        );
        $this->assertSame(
            'AffDPS',
            $view->vars[CreditCardSubForm::BILLING_ADDRESS][CreditCardSubForm::BILLING_ADDRESS_SURNAME]
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Symfony\Component\Form\FormView
     */
    protected function buildCreditCardSubForm(QuoteTransfer $quoteTransfer): FormView
    {
        $formFactory = $this->tester->getFormFactory();

        return $formFactory
            ->create(CreditCardSubForm::class, new BraintreePaymentTransfer(), ['select_options' => []])
            ->setParent($formFactory->create(FakeParentForm::class, $quoteTransfer))
            ->createView();
    }
}
