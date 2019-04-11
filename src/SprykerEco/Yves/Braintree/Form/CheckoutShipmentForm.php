<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Braintree\Form;

use Spryker\Yves\Kernel\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \SprykerEco\Yves\Braintree\BraintreeConfig getConfig()
 */
class CheckoutShipmentForm extends AbstractType
{
    public const FIELD_ID_SHIPMENT_METHOD = 'idShipmentMethod';
    public const OPTION_SHIPMENT_METHODS = 'shipmentMethods';

    public const SHIPMENT_PROPERTY_PATH = 'shipment';
    public const SHIPMENT_SELECTION = 'shipmentSelection';
    public const SHIPMENT_SELECTION_PROPERTY_PATH = self::SHIPMENT_PROPERTY_PATH . '.' . self::SHIPMENT_SELECTION;

    protected const VALIDATION_NOT_BLANK_MESSAGE = 'validation.not_blank';

    public const FORM_NAME = 'checkoutShipmentForm';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return static::FORM_NAME;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAction('/test');

        $this->addIdShipmentMethod($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    protected function addIdShipmentMethod(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(self::FIELD_ID_SHIPMENT_METHOD, ChoiceType::class, [
            'choices' => $options[self::OPTION_SHIPMENT_METHODS],
            'expanded' => true,
            'multiple' => false,
            'required' => true,
            'property_path' => static::SHIPMENT_SELECTION_PROPERTY_PATH,
            'placeholder' => false,
            'constraints' => [
                new NotBlank(),
            ],
            'label' => false,
        ]);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('shipmentMethods');
    }
}
