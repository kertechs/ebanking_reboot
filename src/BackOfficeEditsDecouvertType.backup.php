<?php

namespace App\Form;

use App\Entity\Comptes;
use App\Entity\Operations;
use App\Repository\ClientRepository;
use App\Repository\ComptesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BackofficeEditsDecouvertType extends AbstractType
{
    public function __construct(ComptesRepository $comptesRepository,
                                ClientRepository $clientsRepository,
                                TokenStorageInterface $tokenStorage)
    {
        $this->comptesRepository = $comptesRepository;
        $this->clientsRepository = $clientsRepository;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('decouvert_maximum', TextType::class, [
                'label' => 'Montant maximum du découvert autorisé',
                'required' => true,
                'choi'
            ])
            ->add('decouvert_autorise', ChoiceType::class, [
                'label' => 'Autorisation de découvert',
                'required' => true,
                'placeholder' => 'Autorisation de découvert',
                'choices' => [
                    0 => 'Oui',
                    1 => 'Non',
                ]
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
            'data_class' => Comptes::class,
        ]);
    }
}