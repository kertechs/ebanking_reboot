<?php

namespace App\Controller;

use App\Entity\Demandes;
use App\Entity\Operations;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Client;
use App\Entity\Agences;
use App\Entity\Banques;
use App\Entity\Comptes;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Swift_Attachment;
use Swift_Mailer;


class BackOfficeController extends AbstractController
{
    public function index()
    {
        /*
         * On récupère la liste des inscrits en attente de traitements (par batch de 100 max)
        */
        $clients_repository = $this->getDoctrine()->getRepository(Client::class);
        $_clients = $clients_repository->findAllProspects();

        $clients = $clients_json = [];
        foreach($_clients as $client){
            $clients[] = array(
                'id' => $client->getId(),
                'civilite' => $client->getCivilite(),
                'prenom' => $client->getPrenom(),
                'nom' => $client->getNom(),
                'email' => $client->getEmail(),
                'mobile' => $client->getMobile(),
                'adresse' => $client->getAdresse(),
                'code_postal' => $client->getCodePostal(),
                'ville' => $client->getVille(),
                'Pays' => $client->getPays(),
                'createdAt' => $client->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $client->getUpdatedAt()->format('Y-m-d H:i:s'),
                'createdBy' => $client->getCreatedBy(),
                'updatedBy' => $client->getUpdatedBy(),
                'createdFromIp' => $client->getCreatedFromIp(),
                'updatedFromIp' => $client->getUpdatedFromIp(),
                'status' => $client->getStatus(),
            );
        }
        $clients_json = json_encode($clients);

        /*dump($clients_json);
        dump($clients);
        dd($_clients);*/

        /*
         * On récupère la liste des demandes en attente de traitement
        */
        $demandes_repository = $this->getDoctrine()->getRepository(Demandes::class);
        $demandes = [];
        foreach (array_keys(Demandes::DEMANDES_LABELS) as $const_demande_type)
        {
            $_class = new \ReflectionClass('App\Entity\Demandes');
            $demande_type = $_class->getConstant($const_demande_type);
            dump($demande_type);
            $demandes[$demande_type] = $demandes_repository->findByTypeDemande($demande_type);
        }
        dump($demandes);

        return $this->render('backoffice/index.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'Validations',
            'clients_collection' => $_clients,
            'clients' => $clients,
            'clients_json' => $clients_json,
            'demandes' => $demandes,
        ]);
    }

    public function comptes()
    {
        return $this->render('backoffice/comptes.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'Comptes clients'
        ]);
    }

    public function virements()
    {
        return $this->render('backoffice/virements.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'Virements'
        ]);
    }

    public function bdd()
    {
        return $this->render('backoffice/bdd.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'BDD'
        ]);
    }

    public function plateforme()
    {
        return $this->render('backoffice/plateforme.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'Plateforme Technique'
        ]);
    }

    public function validateRegistration($client_id, $decision
        , EntityManagerInterface $em
        , \Swift_Mailer $mailer
        , UserPasswordEncoderInterface $encoder
        , Request $request
    )
    {
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id);
        if ($client->getId() == $client_id && $decision == "KO")
        {
            //edit status deleted
            $client->setStatus(300);
            $client->setDeletedAt(new \DateTime());
            $em->persist($client);
            $em->flush();

            //Envoyer message email
            $message = (new \Swift_Message('Suite à votre demande d\'inscription sur banquedauphine.online'))
                ->setFrom('contact@banquedauphine.online')
                ->setTo($client->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/notificationRefusCreation.html.twig',[
                            'client' => $client
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);

            //Add flash message
            $this->addFlash('success', 'Notification de refus envoyée à '
                . $client->getCivilite().' '
                . $client->getPrenom().' '
                . $client->getNom().' '
            );
        }
        elseif ($client->getId() == $client_id && $decision == "OK")
        {
            //Associer Agence
            $agence_id = rand(1,5); //C'est naze on fera mieux plus tard (en se basant sur le code postal)
            $agence = $this->getDoctrine()
                ->getRepository(Agences::class)
                ->find($agence_id);
            $bank = $this->getDoctrine()
                ->getRepository(Banques::class)
                ->find(1);

            //Créer compte courant
            $compte = new Comptes();
            $compte->setType(Comptes::COMPTE_COURANT);
            $compte->setAgenceId($agence);
            $compte->setDecouvertAutorise(false);
            $em->persist($compte);
            $em->flush();

            //Editer Client : status = 200 + créer compte (login + mot de passe)
            $client->setStatus(200);
            $client->setUpdatedAt(new \DateTime());
            $client->addCompte($compte);
            $em->persist($client);
            $em->flush();

            $user = new User();
            //$user->setCreatedBy($this->getUser()->getId());
            //$user->setUpdatedBy($this->getUser()->getId());
            $user->setCreatedAt(new \DateTime());
            $user->setUpdatedAt(new \DateTime());
            //$user->setUpdatedBy($this->getUser()->getId());
            $user->setCreatedFromIp($request->getClientIp());
            $user->setUpdatedFromIp($request->getClientIp());
            $user->setEmail($client->getEmail());
            $user->setPassword(
                $encoder->encodePassword($user, $plainpassword = str_pad(
                    rand(1,999999),
                    6,
                    '0',
                    STR_PAD_LEFT
                )
            ));
            $user->setClient($client);
            $em->persist($user);
            $em->flush();

            //Virement de 1000 euros
            $operation = new Operations();
            $operation->setTypeOperation(Operations::TYPE_VIREMENT);
            $operation->setEmetteurCompteId($bank->getBanqueCompteId()->getId());
            $operation->setDestinataireCompteId($compte->getId());
            $operation->setDateExecution(new \DateTime());
            $operation->setMontant(1000);
            if ($operation->execute($em))
            {
                $this->addFlash('success', 'Le virement de bienvenue a bien été effectué');
                $em->persist($operation);
                $em->flush();
            }
            else
            {
                //Add flash message
                $this->addFlash('error', 'Le virement de bienvenue a échoué');
            }

            //Générer PDF bienvenue avec mot de passe
            // Configure Dompdf according to your needs
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');

            // Instantiate Dompdf with our options
            $dompdf = new Dompdf($pdfOptions);

            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('pdf/courrierMotdepasseClient.html.twig', [
                'client' => $client,
                'ClientUser' => $user,
                'ClientPassword' => $plainpassword,
            ]);

            // Load HTML to Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Store PDF Binary Data
            $output = $dompdf->output();

            // In this case, we want to write the file in the public directory
            //$publicDirectory = $this->get('kernel')->getProjectDir() . '/public';
            $publicDirectory = '/tmp/';
            // e.g /var/www/project/public/mypdf.pdf
            $pdfFilepath =  $publicDirectory . '/Bienvenue-'.$client->getId().'.pdf';

            // Write file to the desired path
            file_put_contents($pdfFilepath, $output);


            //Envoi email information
            $message = (new \Swift_Message('Suite à votre demande d\'inscription sur banquedauphine.online'))
                ->setFrom('contact@banquedauphine.online')
                ->setTo($client->getEmail())
                ->attach(Swift_Attachment::fromPath($pdfFilepath))
                ->setBody(
                    $this->renderView(
                        'emails/notificationBienvenue.html.twig',[
                            'client' => $client
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);
            //unlink($pdfFilepath);

            //Add flash message
            $this->addFlash('success', 'Notification d\'accord envoyée à '
                . $client->getCivilite().' '
                . $client->getPrenom().' '
                . $client->getNom().' '
            );
        }

        return $this->redirectToRoute('bankers');
    }
}
