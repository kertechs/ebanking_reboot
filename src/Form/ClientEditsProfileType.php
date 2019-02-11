<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientEditsProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('send_sms_notifications', ChoiceType::class, [
                'label' => 'Notification SMS d\'opération',
                'required' => true,
                'placeholder' => 'Choix',
                'choices' => [
                    //'Sélection' => null,
                    'Oui' => 1,
                    'Non' => 0,
                ]
            ])
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
            /*->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])*/
            ->add('date_naissance', BirthdayType::class, [
                'label' => 'Date de naissance',
                //'attr' => ['class' => 'js-datepicker'],
                'widget' => 'choice',
                'html5' => true,
                'format' => 'dd-MM-yyyy',
                'years' => range(1900,date('Y')-18,1),
                'required' => true,
            ])
            ->add('mobile', TelType::class, [
                'label' => 'Mobile',
                'required' => true,
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
            ])
            ->add('code_postal', TextType::class, [
                'label' => 'Code postal',
                'required' => true,
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'required' => true,
            ])
            ->add('btn_submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'save'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
