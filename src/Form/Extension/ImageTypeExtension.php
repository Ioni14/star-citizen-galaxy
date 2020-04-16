<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ImageTypeExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($options['image_path_property'])) {
            $parentData = $form->getParent()->getData();

            $imageUri = null;
            if ($parentData !== null) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $imageUri = $accessor->getValue($parentData, $options['image_path_property']);
            }

            $view->vars['image_filter_set'] = $options['image_filter_set'] ?? null;
            $view->vars['image_uri'] = $imageUri;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['image_filter_set', 'image_path_property']);
    }

    public static function getExtendedTypes(): iterable
    {
        return [FileType::class];
    }
}
