<?php

namespace App\Form;

use App\Entity\Comptes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BackofficeEditsDecouvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('num_compte')
            //->add('type')
            //->add('solde')
            ->add('decouvert_autorise')
            ->add('decouvert_maximum')
            //->add('cle_rib')
            //->add('iban')
            //->add('createdFromIp')
            //->add('updatedFromIp')
            //->add('createdBy')
            //->add('updatedBy')
            //->add('deletedAt')
            //->add('createdAt')
            //->add('updatedAt')
            //->add('agence_id')
            //->add('compte_client_id')
            ->add('btn_submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'save'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comptes::class,
        ]);
    }
}
