<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\FormType;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Model\TermsVersionEdition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TermsVersionFormType extends AbstractType
{
    public const TERMS_ENTITY = 'terms';

    /* @phpstan-ignore-next-line */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $terms = $options[self::TERMS_ENTITY] ?? new Terms();

        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'required'           => true,
                    'label'              => 'terms_module.admin.edit.label.title',
                    'translation_domain' => 'terms_module',
                    'attr'               => [
                        'placeholder' => 'terms_module.admin.edit.placeholder.title',
                    ],
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'required'           => true,
                    'label'              => 'terms_module.admin.edit.label.content',
                    'translation_domain' => 'terms_module',
                ]
            )
            ->add(
                'isTermsEnabled',
                ChoiceType::class,
                [
                    'required'                  => true,
                    'label'                     => 'terms_module.admin.edit.label.status',
                    'translation_domain'        => 'terms_module',
                    'choice_translation_domain' => 'terms_module',
                    'choices'                   => [
                        'terms_module.admin.edit.status.enabled'  => true,
                        'terms_module.admin.edit.status.disabled' => false,
                    ],
                ]
            )
            ->add(
                'publicationDate',
                DateType::class,
                [
                    'required'           => false,
                    'label'              => 'terms_module.admin.edit.label.publication_date',
                    'translation_domain' => 'terms_module',
                    'widget'             => 'single_text',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                self::TERMS_ENTITY => null,
                'data_class'       => TermsVersionEdition::class,
            ]
        );
    }
}
