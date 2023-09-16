<?php

namespace App\Form;

use App\Entity\Clients;
use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('designation')
            ->add('qte')
            ->add('puht')
            ->add('ttva')
            ->add('remise')
            ->add('timbre')
            ->add('client')
            ->add('client',EntityType::class
               , [
                'class' => Clients::class,
                'choice_label' => 'nom_prenom',
                'label' => 'Client',
                'placeholder' => 'Choisir le client',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
                
                ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
