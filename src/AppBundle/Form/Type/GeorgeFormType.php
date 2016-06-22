<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class GeorgeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'property_path' => 'getRolesAsStringArray',
                'choices' => [
                    'Rôle Administrateur' => 'ROLE_ADMIN',
                    'Rôle Utilisateur' => 'ROLE_USER',
                ]
            ])
        ;
    }
}
