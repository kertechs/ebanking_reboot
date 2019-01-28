<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Comptes;
use App\Entity\Demandes;
use App\Entity\Operations;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class EspaceClientController extends AbstractController
{
    private $em;
    private $request;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();
        $this->em = $em;
        $this->request = $request;
    }

    /**
     * @Route("/espace/client", name="espace_client")
     */
    public function index()
    {
        $client_user = $this->getUser();
        $client_id = $client_user->getClientId();
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id)
        ;
        dump($client);

        $comptes = $client->getComptes();
        dump($comptes);

        return $this->render('espace_client/index.html.twig', [
            'controller_name' => 'EspaceClientController',
            'client' => $client,
            'comptes' => $comptes,
        ]);
    }

    public function detail_compte($compte_id)
    {
        $client_user = $this->getUser();
        $client_id = $client_user->getClientId();
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id)
        ;
        $comptes = $client->getComptes();
        foreach ($comptes as $compte)
        {
            if ($compte->getId() == $compte_id) //User is owner of the account
            {
                $operations = $this->getDoctrine()
                    ->getRepository(Operations::class)
                    ->findByCompte($compte);
                dump($operations);

                return $this->render('espace_client/comptes/details.html.twig', [
                    'controller_name' => 'EspaceClientController',
                    'client' => $client,
                    'compte' => $compte,
                    'operations' => $operations,
                ]);
                break;
            }
        }

        return $this->redirectToRoute('clients_logout');
    }

    public function new_virement()
    {
        $client_user = $this->getUser();
        $client_id = $client_user->getClientId();
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id)
        ;

        return $this->render('espace_client/virements/nouveau.html.twig', [
            'controller_name' => 'EspaceClientController',
            'client' => $client,
        ]);
    }

    public function new_beneficiaire()
    {
        $client_user = $this->getUser();
        $client_id = $client_user->getClientId();
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id)
        ;

        return $this->render('espace_client/virements/beneficiaires/nouveau.html.twig', [
            'controller_name' => 'EspaceClientController',
            'client' => $client,
        ]);
    }

    public function commander($article, $confirm)
    {
        $has_demande = $is_success = false;
        $client_user = $this->getUser();
        $client_id = $client_user->getClientId();
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id)
        ;

        dump($article);
        dump($confirm);

        $demande = new Demandes($article);
        dump($demande);

        if ($article == 'beneficiaire-virement')
        {
            $datas_request = $this->request->getContent();

            $beneficiaire = $this->request->get('beneficiaire');
            $code_bank = str_pad($this->request->get('code_bank'),5, '0', STR_PAD_LEFT);
            $code_agence = str_pad($this->request->get('code_agence'), 5, '0', STR_PAD_LEFT);
            $num_compte = str_pad($this->request->get('num_compte'), 11, '0', STR_PAD_LEFT);
            $cle_rib = str_pad($this->request->get('cle_rib'), 2, '0', STR_PAD_LEFT);
            $iban = 'FR75'.$code_bank.$code_agence.$num_compte.$cle_rib;

            if ($beneficiaire && $code_agence && $code_bank && $cle_rib && $iban)
            {
                $datas = [
                    'beneficiaire' => $beneficiaire,
                    'code_bank' => $code_bank,
                    'code_agence' => $code_agence,
                    'num_compte' => $num_compte,
                    'iban' => $iban,
                ];
                $demande->setDetails($datas);
                dump($datas);

                //Pas de demande existante, on enregistre la demande
                $client->addDemande($demande);
                $this->em->persist($client);
                $this->em->persist($demande);
                $this->em->flush();
                $this->addFlash('success', 'Nous avons bien enregistré votre demande.');
                $is_success = $has_demande = true;

                return $this->redirectToRoute('clients_demander', ['article' => $article]);
            }
        }


        //Check si une demande a déjà été faite
        $existing_demandes = $this->getDoctrine()
            ->getRepository(Demandes::class)
            ->findByTypeByClient($demande->getType(), $client->getId());

        if (count($existing_demandes))
        {
            $has_demande = true;
        }
        dump($existing_demandes);

        if ($confirm == true && !$has_demande && $article != 'beneficiaire-virement')
        {
            //Pas de demande existante, on enregistre la demande
            $client->addDemande($demande);
            $this->em->persist($client);
            $this->em->persist($demande);
            $this->em->flush();
            $this->addFlash('success', 'Nous avons bien enregistré votre demande.');
            $is_success = $has_demande = true;
        }
        elseif ($confirm == true) //should not happen normaly
        {
            $this->addFlash('warning', 'Vous avez déjà une demande enregistrée. Nous vous recontacterons prochainement.');
        }

        return $this->render('espace_client/commander/commande.html.twig', [
            'controller_name' => 'EspaceClientController',
            'client' => $client,
            'is_success' => $is_success,
            'has_demande' => $has_demande,
            'demande' => $demande,
            '_article' => $article,
            'confirm' => $confirm,
        ]);
    }
}
