<?php

namespace App\Form;

use App\Entity\Car;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class AddCarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type')
            ->add('amount', MoneyType::class)
            ->add('image', FileType::class)
            ->add('quantity', NumberType::class)
            ->add('motor', ChoiceType::class, [
                'mapped' => false,
                'constraints' => [new Length(['min' => 2])],
                'invalid_message' => 'Vous devez choisir parmi les choix proposés',
                'choices' => [
                    '-choisir un type de moteur-' => '',
                    'Essence' => 'essence',
                    'Diesel' => 'diesel',
                    'Electrique' => 'electrique',
                    'Hybride' => 'hybride'
                ]])
            ->add('vitesse', ChoiceType::class, [
                'mapped' => false,
                'constraints' => [new Length(['min' => 2])],
                'invalid_message' => 'Vous devez choisir parmi les choix proposés',
                'choices' => [
                    '-choisir une vitesse-' => '',
                    'Automatique' => 'automatique',
                    'Mecanique' => 'mecanique',
                    'Intelligente' => 'intelligente'
                ]])
            ->add('nbSeat', NumberType::class, [
                'mapped' => false,
                'invalid_message' => 'Ce doit être un nombre entier de siège de la voiture'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
