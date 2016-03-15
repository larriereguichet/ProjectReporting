<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class WorkedDayCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('days', CollectionType::class, [
                'entry_type' => WorkedDayType::class
            ]);
    }

    public function getBlockPrefix()
    {
        return 'worked_day_collection';
    }
}
