<?php

namespace App\Form\Type;

use App\Entity\Ship;
use App\Form\Dto\LoanerShipDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoanerShipForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ship', EntityType::class, [
                'required' => true,
                'class' => Ship::class,
                'choice_value' => 'id',
                'choice_label' => 'name',
            ])
            ->add('quantity', IntegerType::class, [
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LoanerShipDto::class,
            'allow_extra_fields' => true,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'loaner_ship';
    }
}
