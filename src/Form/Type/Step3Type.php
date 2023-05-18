<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;


class Step3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dummy', HiddenType::class, [
                'data' => '1',
            ])
            ->add('terms', CheckboxType::class, [
                'label' => "En soumettant ce formulaire, vous acceptez que vos coordonnées et votre réservation soient transmises au prestataire du séminaire dans le but de poursuivre le traitement de votre demande.",
                'constraints' => [
                    new IsTrue([])
                ],
            ])
            ->add('prev', SubmitType::class, [
                'label' => 'Retour',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->add('next', SubmitType::class, [
                'label' => 'Suivant',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
        ;
    }
}