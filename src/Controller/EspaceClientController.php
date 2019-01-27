<?php

namespace App\Controller;

use App\Entity\Client;
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
        $comptes = $client->getComptes();

        dump($client);
        dump($comptes);

        /*$client = $this->em->find(Client::class, $this->getUser()->getClientId());
        dd($client);*/

        return $this->render('espace_client/index.html.twig', [
            'controller_name' => 'EspaceClientController',
        ]);
    }

    public function new_virement()
    {
        return $this->render('espace_client/virements/nouveau.html.twig', [
            'controller_name' => 'EspaceClientController',
        ]);
    }

    public function new_beneficiaire()
    {
        return $this->render('espace_client/virements/beneficiaires/nouveau.html.twig', [
            'controller_name' => 'EspaceClientController',
        ]);
    }
}
