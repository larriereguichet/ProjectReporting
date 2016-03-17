<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddGeorgeProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('georgeId', HiddenType::class)
            ->add('profile', ChoiceType::class, [
                'choices' => $this->transformProjectsToChoices($options['projects'])
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'projects'
        ]);
    }

    /**
     * @param Project[] $projects
     * @return array
     */
    protected function transformProjectsToChoices($projects)
    {
        $choices = [];

        foreach ($projects as $project) {
            $subChoices = [];

            foreach ($project->getProfiles() as $profile) {
                $subChoices[$profile->getName()] = $profile->getId();
            }
            $choices[$project->getName()] = $subChoices;
        }

        return $choices;
    }
}
