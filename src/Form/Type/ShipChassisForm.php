<?php

namespace App\Form\Type;

use App\Entity\Manufacturer;
use App\Form\Dto\ShipChassisDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipChassisForm extends AbstractType
{
    public const MODE_CREATE = 'create';
    public const MODE_EDIT = 'edit';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('manufacturer', EntityType::class, [
                'required' => true,
                'class' => Manufacturer::class,
                'choice_value' => 'id',
                'choice_label' => 'name',
            ]);
        if ($options['mode'] === self::MODE_EDIT) {
            $builder->add('version', HiddenType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShipChassisDto::class,
            'allow_extra_fields' => true,
            'mode' => self::MODE_CREATE,
        ]);
    }
}
