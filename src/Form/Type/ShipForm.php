<?php

namespace App\Form\Type;

use App\Entity\Ship;
use App\Entity\ShipChassis;
use App\Form\Dto\ShipDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('chassis', EntityType::class, [
                'required' => true,
                'class' => ShipChassis::class,
                'choice_value' => 'id',
                'choice_label' => 'name',
            ])
            ->add('size', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'ship.sizes.vehicle' => Ship::SIZE_VEHICLE,
                    'ship.sizes.snub' => Ship::SIZE_SNUB,
                    'ship.sizes.small' => Ship::SIZE_SMALL,
                    'ship.sizes.medium' => Ship::SIZE_MEDIUM,
                    'ship.sizes.large' => Ship::SIZE_LARGE,
                    'ship.sizes.capital' => Ship::SIZE_CAPITAL,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShipDto::class,
            'allow_extra_fields' => true,
        ]);
    }
}
