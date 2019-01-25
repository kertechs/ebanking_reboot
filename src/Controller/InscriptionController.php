<?php

namespace App\Controller;

use App\Form\ClientRegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function index()
    {
        $form = $this->createForm(ClientRegistrationType::class);

        return $this->render('inscription/index.html.twig', [
            'controller_name' => 'InscriptionController',
            'client_registration_form' => $form->createView(),
        ]);
    }
}
