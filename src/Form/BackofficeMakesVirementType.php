<?php

namespace App\Form;

use App\Entity\Operations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BackofficeMakesVirementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_execution')
            ->add('type_operation')
            ->add('credit_debit')
            ->add('montant')
            ->add('emetteur_compte_id')
            ->add('destinataire_compte_id')
            ->add('details')
            ->add('createdFromIp')
            ->add('updatedFromIp')
            ->add('createdBy')
            ->add('updatedBy')
            ->add('deletedAt')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('compte_emetteur')
            ->add('beneficiaire')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Operations::class,
        ]);
    }
}
