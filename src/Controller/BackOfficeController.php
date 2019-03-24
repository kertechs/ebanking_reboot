<?php

namespace App\Controller;

use App\Entity\Beneficiaires;
use App\Entity\CartesBancaires;
use App\Entity\Chequiers;
use App\Entity\Demandes;
use App\Entity\Operations;
use App\Entity\User;
use App\Form\BackofficeEditsClientType;
use App\Form\BackofficeMakesOperationType;
use App\Form\BackofficeEditsDecouvertType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Tests\Command\CommandTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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
    public function __construct(ClientRepository $clientRepository, RequestStack $requestStack)
    {
        $this->client_repository = $clientRepository;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function index()
    {
        /*
         * On récupère la liste des inscrits en attente de traitements (par batch de 100 max)
        */
        $clients_repository = $this->getDoctrine()->getRepository(Client::class);
        $_prospects = $clients_repository->findAllProspects();

        $prospects = $prospects_json = [];
        foreach($_prospects as $prospect){
            $prospects[] = array(
                'id' => $prospect->getId(),
                'civilite' => $prospect->getCivilite(),
                'prenom' => $prospect->getPrenom(),
                'nom' => $prospect->getNom(),
                'email' => $prospect->getEmail(),
                'mobile' => $prospect->getMobile(),
                'adresse' => $prospect->getAdresse(),
                'code_postal' => $prospect->getCodePostal(),
                'ville' => $prospect->getVille(),
                'Pays' => $prospect->getPays(),
                'createdAt' => $prospect->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $prospect->getUpdatedAt()->format('Y-m-d H:i:s'),
                'createdBy' => $prospect->getCreatedBy(),
                'updatedBy' => $prospect->getUpdatedBy(),
                'createdFromIp' => $prospect->getCreatedFromIp(),
                'updatedFromIp' => $prospect->getUpdatedFromIp(),
                'status' => $prospect->getStatus(),
            );
        }
        $prospects_json = json_encode($prospects);

        /*
         * On récupère la liste des demandes en attente de traitement
        */
        $demandes_repository = $this->getDoctrine()->getRepository(Demandes::class);
        $demandes = [];
        foreach (array_keys(Demandes::DEMANDES_LABELS) as $const_demande_type)
        {
            $_class = new \ReflectionClass('App\Entity\Demandes');
            $demande_type = $_class->getConstant($const_demande_type);
            $demandes[$demande_type] = $demandes_repository->findByTypeDemande($demande_type);
        }
        dump($demandes);

        /*
         * Cas particulier des demandes d'ajout de bénéficiaire
         * On va chercher à indiquer au gestionnaire si l'iban demandé existe ou pas
         */
        //dd($demandes);
        foreach ($demandes[Demandes::DEMANDE_DESTINATAIRE_VIREMENT] as $_idx =>  $_demande)
        {
            $_demande_details = $_demande->getDetails();
            $_demande_details['matching_compte_found'] = null;
            $_iban_ok = false;
            if (is_array($_demande_details) && isset($_demande_details['iban']) && $iban = $_demande_details['iban'])
            {
                $comptes_repository = $this->getDoctrine()->getRepository(Comptes::class);
                $_comptes = $comptes_repository->findByIban($iban);
                if (is_array($_comptes) && count($_comptes) == 1)
                {
                    $_iban_ok = true;
                    $_matching_compte = $_comptes[0];

                    $_demande_details['matching_compte_found'] = [
                        'type' => $_matching_compte->getType(),
                        'agence' => $_matching_compte->getAgenceId()->getNom(),
                        //'prenom' => $_matching_compte->getClient()->getPrenom(),
                        //'nom' => $_matching_compte->getClient()->getNom(),
                        'createdAt' => $_matching_compte->getCreatedAt()->format('d/m/Y'),
                        'iban' => $_matching_compte->getIban(),
                    ];
                }
            }
            $_demande_details['iban_is_ok'] = $_iban_ok;
            $_demande->setDetails($_demande_details);
            $demandes[Demandes::DEMANDE_DESTINATAIRE_VIREMENT][$_idx] = $_demande;
        }
        //dump($demandes);


        return $this->render('backoffice/index.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'Validations',
            'clients_collection' => $_prospects,
            'clients' => $prospects,
            'clients_json' => $prospects_json,
            'demandes' => $demandes,
        ]);
    }

    public function comptes()
    {
        $clientRepository = $this->client_repository;
        $clients = $clientRepository->findByStatus(Client::CURRENT);
        foreach ($clients as $idx => $client)
        {
            $operations = $this->getDoctrine()
                ->getRepository(Operations::class)
                ->findByClient($client);
            $client->setOperations($operations);
            $clients[$idx] = $client;
        }

        dump($clients);

        return $this->render('backoffice/comptes.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'Comptes clients',
            'clients' => $clients,
        ]);
    }

    public function virements(EntityManagerInterface $em)
    {
        $request = $this->request;
        $form = $this->createForm(BackofficeMakesOperationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var $operation Operations
             */
            $operation = $form->getData();
            //dd($operation);
            $operation->setTypeOperation(Operations::TYPE_VIREMENT_BACKOFFICE);
            $operation->setDateExecution( new \DateTime() );
            if ($operation_result = $operation->execute($em))
            {
                $this->addFlash('success', 'Votre opération a bien été enregistrée');
            }
            else
            {
                $this->addFlash('error', 'Votre opération n\'a pas pu être enregistrée. Vérifiez le solde du compte');
            }
            $em->persist($operation);
            $em->flush();

            return $this->redirectToRoute('bankers_virements');
        }

        return $this->render('backoffice/virements.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'Virements',
            'backoffice_virement_form' => $form->createView()
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

    public function validateDemande($demande_id, $decision, $additional_param
        , EntityManagerInterface $em
        , \Swift_Mailer $mailer
        , Request $request
    )
    {
        //dd($additional_param);
        $demande = $this->getDoctrine()
            ->getRepository(Demandes::class)
            ->find($demande_id);

        //On ne traite pas une demande qui n'est plus en status ::STATUS_NEW

        if ($demande->getId() == $demande_id && $decision == "KO" && $demande->getStatus() == Demandes::STATUS_NEW)
        //Demande refusée
        {
            //edit status deleted
            $demande->setStatus(Demandes::STATUS_REFUSED);
            $demande->setDeletedAt(new \DateTime());
            $em->persist($demande);
            $em->flush();

            //Envoyer message email
            $client = $demande->getClient();
            $message = (new \Swift_Message('Suite à votre demande d\'inscription sur banquedauphine.online'))
                ->setFrom('contact@banquedauphine.online')
                ->setTo($demande->getClient()->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/notificationRefusDemande.html.twig',[
                            'client' => $client,
                            'demande' => $demande,
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
        elseif ($demande->getId() == $demande_id && $decision == "OK" && $demande->getStatus() == Demandes::STATUS_NEW)
        {
            $demande_ok = false;
            $client = $demande->getClient();
            switch ($demande->getType())
            {
                case Demandes::DEMANDE_CB:
                    $cb = new CartesBancaires();
                    $cb->setClient($client);
                    $cb->setCompte($client->getCompteCourant());
                    $em->persist($cb);

                    $demande->setStatus(Demandes::STATUS_GRANTED);
                    $em->persist($demande);
                    $em->flush();

                    $cb_password = rand(1000,9999);

                    $this->sendCourrierClient($client,[
                        'twig_template_file_name' => 'EnvoiCarteBleue',
                        'twig_template_name' => 'courrierEnvoiCarteBleueClient.html.twig',
                        'twig_parameters' => [
                            'client' => $client,
                        ],
                        'swift_template_name' => 'notificationCourrierEnvoiCarteBleueClient.html.twig',
                        'swift_parameters' => [
                            'client' => $client,
                        ],
                        'swift_subject' => 'Envoi de votre carte bleue',
                    ], $mailer);

                    $this->sendCourrierClient($client,[
                        'twig_template_file_name' => 'EnvoiMotDePasseCarteBleue',
                        'twig_template_name' => 'courrierEnvoiMotDePasseCarteBleueClient.html.twig',
                        'twig_parameters' => [
                            'client' => $client,
                            'cb_password' => $cb_password,
                        ],
                        'swift_template_name' => 'notificationCourrierEnvoiMotDePasseCarteBleueClient.html.twig',
                        'swift_parameters' => [
                            'client' => $client,
                        ],
                        'swift_subject' => 'Envoi du mot de passe de votre carte bleue',
                    ], $mailer);


                    $demande_ok = true;
                    $this->addFlash('success', 'Carte bleue créée et expédiée.');

                    break;

                case Demandes::DEMANDE_CHEQUIER:
                    $chequier = new Chequiers();
                    $chequier->setClient($client);
                    $chequier->setCompte($client->getCompteCourant());
                    $em->persist($chequier);

                    $demande->setStatus(Demandes::STATUS_GRANTED);
                    $em->persist($demande);
                    $em->flush();

                    $this->sendCourrierClient($client,[
                        'twig_template_file_name' => 'EnvoiChequier',
                        'twig_template_name' => 'courrierEnvoiChequierClient.html.twig',
                        'twig_parameters' => [
                            'client' => $client,
                        ],
                        'swift_template_name' => 'notificationCourrierEnvoiChequierClient.html.twig',
                        'swift_parameters' => [
                            'client' => $client,
                        ],
                        'swift_subject' => 'Envoi de votre chequier',
                    ], $mailer);

                    $demande_ok = true;
                    $this->addFlash('success', 'Chéquier expédié.');

                    break;

                case Demandes::DEMANDE_COMPTE_EPARGNE:
                    $new_compte = new Comptes();
                    $new_compte->setType(Comptes::COMPTE_EPARGNE);
                    $em->persist($new_compte);

                    $client->addCompte($new_compte);
                    $em->persist($client);

                    //On ajoute immédiatement les nouveaux comptes épargnes et joints en tant que bénéficiaires
                    $beneficiaire = new Beneficiaires();
                    $beneficiaire->setClient($client);
                    $beneficiaire->setCompte($new_compte);
                    $beneficiaire->setNom($new_compte->getTypeLbl());
                    $em->persist($beneficiaire);

                    $demande->setStatus(Demandes::STATUS_GRANTED);
                    $em->persist($demande);

                    $em->flush();
                    $demande_ok = true;
                    $this->addFlash('success', 'Compte épargne ajouté');
                    break;

                case Demandes::DEMANDE_COMPTE_JOINT:
                    /*todo: prise en compte 2eme titulaire de compte*/
                    $new_compte = new Comptes();
                    $new_compte->setType(Comptes::COMPTE_JOINT);
                    $em->persist($new_compte);

                    $client->addCompte($new_compte);
                    $em->persist($client);

                    /*$client2->addCompte($new_compte);
                    $em->persist($client2);*/

                    //On ajoute immédiatement les nouveaux comptes épargnes et joints en tant que bénéficiaires
                    $beneficiaire = new Beneficiaires();
                    $beneficiaire->setClient($client);
                    $beneficiaire->setCompte($new_compte);
                    $beneficiaire->setNom($new_compte->getTypeLbl());
                    $em->persist($beneficiaire);

                    /*$beneficiaire2 = new Beneficiaires();
                    $beneficiaire2->setClient($client2);
                    $beneficiaire2->setCompte($new_compte);
                    $beneficiaire2->setNom($new_compte->getTypeLbl());
                    $em->persist($beneficiaire2);*/

                    $demande->setStatus(Demandes::STATUS_GRANTED);
                    $em->persist($demande);

                    $em->flush();

                    $demande_ok = true;
                    $this->addFlash('success', 'Compte joint ajouté');
                    break;

                case Demandes::DEMANDE_DESTINATAIRE_VIREMENT:
                    $demande_details = $demande->getDetails();
                    $demande_details['matching_compte_found'] = null;
                    $iban_ok = false;
                    if (is_array($demande_details) && isset($demande_details['iban']) && $iban = $demande_details['iban'])
                    {
                        $comptes_repository = $this->getDoctrine()->getRepository(Comptes::class);
                        $comptes = $comptes_repository->findByIban($iban);
                        $compte = (isset($comptes[0]))?$comptes[0]:null;
                        dump($iban);
                        dump($comptes);
                        dump($compte);

                        dump('is_array($comptes) => ' . is_array($comptes));
                        dump('count($comptes) => ' . count($comptes));
                        dump('$compte->getIban() => ' . $compte->getIban());
                        dump('is_null($compte) => ' . is_null($compte));
                        dump('$compte == $client->getCompteCourant() => ' . ($compte == $client->getCompteCourant()));

                        //dd('');
                        //dd($comptes);
                        if (is_array($comptes) && count($comptes) == 1 &&  $compte->getIban() == $iban && !is_null($compte))
                        {
                            if ($compte == $client->getCompteCourant()){
                                $this->addFlash('error', 'Echec de la validation de la demande : impossible d\'ajouter son propre compte "'.$iban.'".');
                            }
                            else {
                                $iban_ok = true;
                                $matching_compte = $comptes[0];

                                /*$demande_details['matching_compte_found'] = [
                                    'type' => $matching_compte->getType(),
                                    'agence' => $matching_compte->getAgenceId()->getNom(),
                                    'createdAt' => $matching_compte->getCreatedAt()->format('d/m/Y'),
                                    'iban' => $_matching_compte->getIban(),
                                ];*/

                                $beneficiaire = new Beneficiaires();
                                $beneficiaire->setClient($client);
                                $beneficiaire->setCompte($compte);
                                $beneficiaire->setNom($demande_details['beneficiaire']);
                                $em->persist($beneficiaire);

                                $demande->setStatus(Demandes::STATUS_GRANTED);
                                $em->persist($demande);

                                $em->flush();
                                //dd($matching_compte);

                                //Add flash message
                                $this->addFlash('success', 'Compte destinataire ajouté');
                            }
                        }
                        else
                        {
                            //Add flash message
                            $this->addFlash('error', 'Echec de la validation de la demande : compte "'.$iban.'" non trouvé');
                        }
                    }
                    break;

                case Demandes::DEMANDE_DECOUVERT_AUTORISE:
                    $decouvert_autorise = true;
                    $decouvert_montant_max = abs((int)$additional_param);
                    $client->setHasDecouvertAutorise = true;
                    $em->persist($client);

                    $comptes = $client->getComptes();
                    foreach ($comptes as $compte)
                    {
                        if ($compte->getType() == Comptes::COMPTE_COURANT) //découvert autorisé sur compte courant uniquement
                        {
                            $compte->setDecouvertAutorise($decouvert_autorise);
                            $compte->setDecouvertMaximum($decouvert_montant_max);
                            $em->persist($compte);

                            $demande->setStatus(Demandes::STATUS_GRANTED);
                            $em->persist($demande);

                            $em->flush();

                            $this->addFlash('success', 'Autorisation de découvert en place sur le compte '.$compte->getIban());
                        }
                    }
                    break;
            }
        }

        return $this->redirectToRoute('bankers');
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
            $client->setAgence($agence);
            $em->persist($client);
            $em->flush();

            //Ajouter le compte courant à la liste des bénéficiaires
            $beneficiaire = new Beneficiaires();
            $beneficiaire->setCompte($compte);
            $beneficiaire->setClient($client);
            $em->persist($beneficiaire);
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

            /*//Générer PDF bienvenue avec mot de passe
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
            //unlink($pdfFilepath);*/

            $this->sendCourrierClient($client,[
                'twig_template_file_name' => 'Bienvenue',
                'twig_template_name' => 'courrierMotdepasseClient.html.twig',
                'twig_parameters' => [
                    'client' => $client,
                    'ClientUser' => $user,
                    'ClientPassword' => $plainpassword,
                ],
                'swift_template_name' => 'notificationBienvenue.html.twig',
                'swift_parameters' => [
                    'client' => $client,
                ],
                'swift_subject' => 'Suite à votre demande d\'inscription sur banquedauphine.online',
            ], $mailer);

            //Add flash message
            $this->addFlash('success', 'Notification d\'accord envoyée à '
                . $client->getCivilite().' '
                . $client->getPrenom().' '
                . $client->getNom().' '
            );
        }

        return $this->redirectToRoute('bankers');
    }

    function sendCourrierClient(Client $client, $courrier_options, \Swift_Mailer $mailer)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);

        $html = $this->renderView('pdf/'.$courrier_options['twig_template_name'], $courrier_options['twig_parameters']);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $publicDirectory = '/tmp/';
        $pdfFilepath =  $publicDirectory . '/'.$courrier_options['twig_template_file_name'].'-'.$client->getId().'.pdf';
        file_put_contents($pdfFilepath, $output);


        //Envoi email information
        $message = (new \Swift_Message($courrier_options['swift_subject']))
            ->setFrom('contact@banquedauphine.online')
            ->setTo($client->getEmail())
            ->attach(Swift_Attachment::fromPath($pdfFilepath))
            ->setBody(
                $this->renderView(
                    'emails/'.$courrier_options['swift_template_name'],
                    $courrier_options['swift_parameters']
                ),
                'text/html'
            );
        $mailer->send($message);
        //unlink($pdfFilepath);
    }

    public function list_demandes($client_id)
    {
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id);

        $request = $this->request;
        $demandes_repository = $this->getDoctrine()->getRepository(Demandes::class);
        $demandes = $demandes_repository->findBy([
            'status' => [
                200,
                300,
            ],
            'client' => $client,
        ]);

        dump($demandes);

        return $this->render('backoffice/list_demandes.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'Historique demandes',
            'demandes' => $demandes,
        ]);
    }

    public function edit_client($client_id, EntityManagerInterface $em)
    {
        $request = $this->request;
        $clients_repository = $this->getDoctrine()->getRepository(Client::class);
        $client = $clients_repository->findOneBy([
            'id' => $client_id,
        ]);

        $form = $this->createForm(BackofficeEditsClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();
            $client->setUpdatedFromIp($request->getClientIp());

            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'La modification a bien été enregistrée');

            return $this->redirectToRoute('bankers_client_edit', ['client_id' => $client_id]);
        }

        return $this->render('backoffice/edit_client.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'Edition client',
            'backoffice_client_form' => $form->createView(),
        ]);
    }

    public function edit_decouvert($compte_id, EntityManagerInterface $em)
    {
        $request = $this->request;
        $comptes_repository = $this->getDoctrine()->getRepository(Comptes::class);
        $compte = $comptes_repository->findOneBy([
            'id' => $compte_id,
        ]);
        dump($compte);

        $compte = $this->getDoctrine()
            ->getRepository(Comptes::class)
            ->find($compte_id)
        ;

        $form = $this->createForm(BackofficeEditsDecouvertType::class, $compte);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $compte = $form->getData();
            $compte->setUpdatedFromIp($request->getClientIp());

            $em->persist($compte);
            $em->flush();

            $this->addFlash('success', 'La modification a bien été enregistrée');

            return $this->redirectToRoute('bankers_decouvert_edit', ['compte_id' => $compte_id]);
        }

        return $this->render('backoffice/edit_decouvert.html.twig', [
            'controller_name' => 'BackOfficeController',
            'context_entry' => 'Edition decouvert autorisé',
            'backoffice_decouvert_form' => $form->createView(),
        ]);
    }
}
