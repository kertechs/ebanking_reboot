<?php

namespace App\Form;

use App\Entity\Beneficiaires;
use App\Entity\Client;
use App\Entity\Comptes;
use App\Entity\Operations;
use App\Repository\BeneficiairesRepository;
use App\Repository\ClientRepository;
use App\Repository\ComptesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ClientMakesOperationType extends AbstractType
{
    public function __construct(ComptesRepository $comptesRepository,
                                BeneficiairesRepository $beneficiairesRepository,
                                ClientRepository $clientsRepository,
                                TokenStorageInterface $tokenStorage)
    {
        $this->comptesRepository = $comptesRepository;
        $this->beneficiairesRepository = $beneficiairesRepository;
        $this->clientsRepository = $clientsRepository;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $client_user = $this->user;
        $client_id = $client_user->getClientId();
        $client = $this->clientsRepository
            ->find($client_id)
        ;

        $builder
//            ->add('date_execution')
//            ->add('type_operation')
//            ->add('credit_debit')
            ->add('montant', TextType::class, [
                'label' => 'Montant',
                'required' => true,
            ])
            ->add('compte_emetteur', EntityType::class, [
                'label' => 'Compte emetteur',
                'choice_label' => function(Comptes $compte){
                    return sprintf('%s %s (%s €)',
                        $compte->getTypeLbl(),
                        $compte->getIban(),
                        number_format($compte->getSolde(), 2, ',', ' ')
                    );

                    //return $compte->getTypeLbl().' '.$compte->getIban().' ('.number_format($compte->getSolde(), 2, ',', ' '.')';
                },
                'choices' => $this->clientsRepository->findAllComptes($client),
                'placeholder' => 'Sélectionner un compte emetteur',
                'required' => true,
                'class' => Comptes::class,
            ])
            ->add('beneficiaire', EntityType::class, [
                'label' => 'Compte bénéficiaire',
                'choice_label' => function(Beneficiaires $beneficiaire){
                    return sprintf('%d:%s - %s %s (%s €)',
                        $beneficiaire->getId(),
                        $beneficiaire->getNom(),
                        $beneficiaire->getCompte()->getTypeLbl(),
                        $beneficiaire->getCompte()->getIban(),
                        number_format($beneficiaire->getCompte()->getSolde(), 2, ',', ' ')
                    );

                    //return $compte->getTypeLbl().' '.$compte->getIban().' ('.number_format($compte->getSolde(), 2, ',', ' '.')';
                },
                'choices' => $this->clientsRepository->findAllBeneficiaires($client),
                'placeholder' => 'Sélectionner un compte bénéficiaire',
                'required' => true,
                'class' => Beneficiaires::class,
            ])
            ->add('details', TextType::class, [
                'label' => 'Détails',
                'required' => false,
            ])
            ->add('btn_submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'save'],
            ])
//            ->add('createdFromIp')
//            ->add('updatedFromIp')
//            ->add('createdBy')
//            ->add('updatedBy')
//            ->add('deletedAt')
//            ->add('createdAt')
//            ->add('updatedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Operations::class,
        ]);
    }
}
