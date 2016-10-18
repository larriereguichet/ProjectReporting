<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Role\Role;

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
                'choices' => [
                    'Rôle Administrateur' => 'ROLE_ADMIN',
                    'Rôle Utilisateur' => 'ROLE_USER',
                ]
            ])
        ;
        $builder
            ->get('roles')
            ->addModelTransformer(new CallbackTransformer(function ($roles) {
                $rolesAsString = [];

                if (is_array($roles)) {
                    foreach ($roles as $role) {
                        if ($role instanceof Role) {
                            $rolesAsString[] = $role->getRole();
                        } else {
                            $rolesAsString[] = $role;
                        }
                    }
                }

                return $rolesAsString;

            }, function ($roles) {
                return $roles;
            }));
    }
}
