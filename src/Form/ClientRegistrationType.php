<?php

namespace App\Form;

use App\Entity\Client;
use Doctrine\DBAL\Driver\OCI8\Driver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civilite', ChoiceType::class, [
                'label' => 'Civilité',
                'required' => true,
                'placeholder' => 'Civilité',
                'choices' => [
                    //'Sélection' => null,
                    'Madame' => 'Madame',
                    'Monsieur' => 'Monsieur',
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('mobile', TelType::class, [
                'label' => 'Mobile',
                'required' => true,
            ])
            ->add('pays', HiddenType::class, [
                'data' => 'France',
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'required' => true,
            ])
            ->add('code_postal', TextType::class, [
                'label' => 'Code postal',
                'required' => true,
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
            ])
//            ->add('status')
//            ->add('createdAt')
//            ->add('updatedAt')
//            ->add('deletedAt')
//            ->add('createdBy')
//            ->add('updatedBy')
//            ->add('createdFromIp')
//            ->add('updatedFromIp')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
