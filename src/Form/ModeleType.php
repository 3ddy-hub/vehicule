<?php

namespace App\Form;

use App\Entity\Modele;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ModeleType
 * @package App\Form
 */
class ModeleType extends AbstractType
{
    private $translator;

    /**
     * ModeleType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modele', TextType::class, [
                'attr'  => [
                    'class' => 'form-control',
                ],
                'label' => $this->translator->trans('bo.model')
            ])
            ->add('marque', TextType::class, [
                'attr'  => [
                    'class' => 'form-control',
                ],
                'label' => $this->translator->trans('bo.brand')
            ])
            ->add('puissance', TextType::class, [
                'attr'  => [
                    'class' => 'form-control',
                ],
                'label' => $this->translator->trans('bo.puissance')
            ])
            ->add('carburant', TextType::class, [
                'attr'  => [
                    'class' => 'form-control',
                ],
                'label' => $this->translator->trans('bo.carburant')
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Modele::class,
        ]);
    }
}
