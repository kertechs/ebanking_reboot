<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientMakesVirementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom')
            ->add('nom')
            ->add('email')
            ->add('mobile')
            ->add('pays')
            ->add('ville')
            ->add('code_postal')
            ->add('adresse')
            ->add('status')
            ->add('civilite')
            ->add('has_cb')
            ->add('has_chequier')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            ->add('createdBy')
            ->add('updatedBy')
            ->add('createdFromIp')
            ->add('updatedFromIp')
            ->add('comptes')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
