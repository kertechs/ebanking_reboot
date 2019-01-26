<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BackOfficeController extends AbstractController
{
    public function index()
    {
        return $this->render('backoffice/index.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'Accueil'
        ]);
    }

    public function dashboard()
    {

    }
}
