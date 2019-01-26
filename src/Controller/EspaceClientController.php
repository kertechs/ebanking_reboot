<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EspaceClientController extends AbstractController
{
    /**
     * @Route("/espace/client", name="espace_client")
     */
    public function index()
    {
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
