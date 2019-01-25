<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InscriptionController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function index(EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(ClientRegistrationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();
            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Merci pour votre inscription');

            return $this->redirectToRoute('merci');
        }

        return $this->render('inscription/index.html.twig', [
            'controller_name' => 'InscriptionController',
            'client_registration_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/merci", name="merci")
     */
    public function merci(EntityManagerInterface $em, Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $_success = $flashbag->peek('success');
        if (! (is_array($_success) && count($_success)) ){
            return $this->redirectToRoute('homepage');
        }

        return $this->render('inscription/merci.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }

}
