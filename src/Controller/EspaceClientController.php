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
}
