<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegisterEventFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('confirm', CheckboxType::class, [
                'label' => 'J\'accepte les conditions générales de vente',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You must agree to the terms of service',
                    ]),
                ],
            ])
        ;
    }

}