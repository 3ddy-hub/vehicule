<?php

namespace App\Form;

use App\Entity\Modele;
use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class VoitureType
 * @package App\Form
 */
class VoitureType extends AbstractType
{
    private $translator;

    /**
     * VoitureType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('immatriculation', TextType::class, [
                'label' => $this->translator->trans('bo.immatriculation'),
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('couleur', TextType::class, [
                'label' => $this->translator->trans('bo.color'),
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('kilometrage', TextType::class, [
                'label' => $this->translator->trans('bo.km'),
                'attr'  => [
                    'class' => 'form-control'
                ]
            ])
            ->add('modele', EntityType::class, [
                'label'        => $this->translator->trans('bo.model'),
                'attr'         => [
                    'class' => 'form-control'
                ],
                'class'        => Modele::class,
                'choice_label' => 'modele'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
