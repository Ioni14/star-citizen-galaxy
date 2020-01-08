<?php

namespace App\Form\Type;

use App\Entity\Ship;
use App\Entity\ShipCareer;
use App\Entity\ShipChassis;
use App\Entity\ShipRole;
use App\Form\Dto\HoldedShipDto;
use App\Form\Dto\ShipDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipForm extends AbstractType
{
    public const MODE_EDIT = 'edit';
    public const MODE_CREATE = 'create';

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
            ->add('holdedShips', CollectionType::class, [
                'entry_type' => HoldedShipForm::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_data' => new HoldedShipDto(),
            ])
            ->add('height', NumberType::class, [
                'required' => false,
                'scale' => 2,
            ])
            ->add('length', NumberType::class, [
                'required' => false,
                'scale' => 2,
            ])
            ->add('minCrew', IntegerType::class, [
                'required' => false,
            ])
            ->add('maxCrew', IntegerType::class, [
                'required' => false,
            ])
            ->add('size', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'N/A',
                'choices' => [
                    'ship.sizes.vehicle' => Ship::SIZE_VEHICLE,
                    'ship.sizes.snub' => Ship::SIZE_SNUB,
                    'ship.sizes.small' => Ship::SIZE_SMALL,
                    'ship.sizes.medium' => Ship::SIZE_MEDIUM,
                    'ship.sizes.large' => Ship::SIZE_LARGE,
                    'ship.sizes.capital' => Ship::SIZE_CAPITAL,
                ],
            ])
            ->add('cargoCapacity', IntegerType::class, [
                'required' => false,
            ])
            ->add('price', MoneyType::class, [
                'required' => false,
                'divisor' => 100,
                'currency' => 'USD',
            ])
            ->add('readyStatus', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'N/A',
                'choices' => [
                    'ship.ready_statuses.flight_ready' => Ship::READY_STATUS_FLIGHT_READY,
                    'ship.ready_statuses.concept' => Ship::READY_STATUS_CONCEPT,
                ],
            ])
            ->add('career', EntityType::class, [
                'required' => false,
                'class' => ShipCareer::class,
                'placeholder' => 'N/A',
                'choice_value' => 'id',
                'choice_label' => 'label',
            ])
            ->add('roles', EntityType::class, [
                'required' => false,
                'class' => ShipRole::class,
                'multiple' => true,
                'choice_value' => 'id',
                'choice_label' => 'label',
            ])
            ->add('pledgeUrl', UrlType::class, [
                'required' => false,
            ])
            ->add('picture', FileType::class, [
                'required' => false,
                'image_path_property' => 'picturePath',
                'image_assets_package' => 'ship_pictures',
            ])
            ->add('thumbnail', FileType::class, [
                'required' => false,
                'image_path_property' => 'thumbnailPath',
                'image_assets_package' => 'ship_thumbnails',
            ]);

        if ($options['mode'] === self::MODE_EDIT) {
            $builder->add('version', HiddenType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShipDto::class,
            'allow_extra_fields' => true,
            'mode' => self::MODE_CREATE,
        ]);
    }
}
