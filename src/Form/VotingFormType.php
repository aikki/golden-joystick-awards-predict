<?php

namespace App\Form;

use App\Entity\Award;
use App\Entity\Nominee;
use App\Entity\Voter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VotingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['award']->getCategories() as $category) {
            $builder->add("category_{$category->getId()}", EntityType::class, [
                'mapped' => false,
                'class' => Nominee::class,
                'choices' => $category->getNominees()->toArray(),
                'expanded' => true,
                'multiple' => false,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => false,
            ]);
        }
        $builder
            ->add('name')
            ->add('signature_font', HiddenType::class);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options) {
            $form = $event->getForm();
            $voter = $event->getData();
            foreach ($options['award']->getCategories() as $category) {
                $nominee = $form->get("category_{$category->getId()}")->getData();
                if ($nominee === null) continue;
                if ($nominee->getCategory() !== $category) {
                    $form->get("category_{$category->getId()}")->addError(new FormError('Invalid nominee'));
                    continue;
                }
                $voter->addVotePlain($category, $nominee);
            }
            if (count($voter->getVotes()) === 0) {
                $form->addError(new FormError('No nominee selected'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voter::class,
            'award' => null,
        ]);

        $resolver->setAllowedTypes('award', Award::class);
    }

    public function getBlockPrefix(): string
    {
        return 'voting';
    }
}
