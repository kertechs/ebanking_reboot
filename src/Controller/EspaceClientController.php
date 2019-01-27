<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Comptes;
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

    public function commander($article)
    {
        $client_user = $this->getUser();
        $client_id = $client_user->getClientId();
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id)
        ;

        return $this->render('espace_client/commander/'.$article.'.html.twig', [
            'controller_name' => 'EspaceClientController',
            'client' => $client,
        ]);
    }
}
