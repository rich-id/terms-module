<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\FormType;

use Doctrine\ORM\EntityRepository;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Model\SignatureListForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SignatureListFormType extends AbstractType
{
    /* @phpstan-ignore-next-line */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'search',
                TextType::class,
                [
                    'required' => false,
                    'attr'     => [
                        'placeholder' => 'terms_module.pdf_signature.placeholder.search',
                    ],
                    'translation_domain' => 'terms_module',
                    'label'              => false,
                ]
            )
            ->add(
                'terms',
                EntityType::class,
                [
                    'required'           => false,
                    'class'              => Terms::class,
                    'choice_label'       => 'name',
                    'placeholder'        => 'terms_module.pdf_signature.placeholder.terms',
                    'translation_domain' => 'terms_module',
                    'label'              => false,
                    'query_builder'      => static function (EntityRepository $er) {
                        return $er->createQueryBuilder('t')->orderBy('t.name', 'ASC');
                    },
                ]
            )
            ->add(
                'numberItemsPerPage',
                ChoiceType::class,
                [
                    'required'           => true,
                    'empty_data'         => 10,
                    'label'              => 'terms_module.pdf_signature.placeholder.number_items_per_page',
                    'translation_domain' => 'terms_module',
                    'choices'            => [
                        10 => 10,
                        20 => 20,
                        50 => 50,
                    ],
                ]
            )
            ->add(
                'page',
                HiddenType::class,
                [
                    'required'   => false,
                    'empty_data' => 1,
                ]
            )
            ->add(
                'sort',
                HiddenType::class,
                [
                    'required'   => false,
                    'empty_data' => SignatureListForm::SORT_SIGNATORY,
                ]
            )
            ->add(
                'sortDirection',
                HiddenType::class,
                [
                    'required'   => false,
                    'empty_data' => SignatureListForm::SORT_DESC,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => SignatureListForm::class,
                'method'     => 'GET',
            ]
        );
    }
}
