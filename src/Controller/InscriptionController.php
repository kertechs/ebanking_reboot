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
    public function index(EntityManagerInterface $em, Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(ClientRegistrationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();

            $EmailAlreadyExist = $em->getRepository(Client::class)->createQueryBuilder('C')
                ->where('C.email = :_ClientEmail')
                ->andWhere('C.deletedAt IS NULL')
                ->setParameter('_ClientEmail', $client->getEmail())
                ->getQuery()
                ->getResult();

            if (!count($EmailAlreadyExist)) //Email not found
            {
                $em->persist($client);
                $em->flush();

                $this->addFlash('success', 'Merci pour votre inscription');

                /*Notify the client*/
                $message = (new \Swift_Message('Merci pour votre demande d\'inscription sur banquedauphine.online'))
                    ->setFrom('contact@banquedauphine.online')
                    ->setTo($client->getEmail())
                    ->setBody(
                        $this->renderView(
                        // templates/emails/registration.html.twig
                            'emails/registration.html.twig',[
                                'client' => $client
                            ]
                        ),
                        'text/html'
                    )
                    /*
                     * If you also want to include a plaintext version of the message
                     */
                    ->addPart(
                        $this->renderView(
                            'emails/registration.txt.twig',[
                                'client' => $client
                            ]
                        ),
                        'text/plain'
                    )
                ;
                $mailer->send($message);
            }
            else {
                $this->addFlash('error', 'Nous sommes désolés. Cette adresse email est déjà enregistrée');
            }

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
        $_error = $flashbag->peek('error');
        if (! (is_array($_success) && count($_success)) && ! (is_array($_error) && count($_error)) ){
            return $this->redirectToRoute('homepage');
        }

        return $this->render('inscription/merci.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }

}
