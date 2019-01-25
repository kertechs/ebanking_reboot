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
            $data = $form->getData();
            $client = $form->getData();
            //dump('data = ' . print_r($data, true));

            /*$client = new Client();
            dd($client);
            $client->setPrenom($data['prenom']);
            $client->setNom($data['nom']);
            $client->setEmail($data['email']);
            $client->setMobile($data['mobile']);
            $client->setCivilite($data['civilite']);
            $client->setAdresse($data['adresse']);
            $client->setCodePostal($data['code_postal']);
            $client->setVille($data['ville']);
            $client->setPays($data['pays']);*/
            //dd($client);
            //dd($form->getData());

            $em->persist($client);
            $em->flush();
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
        return $this->render('inscription/merci.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }

}
