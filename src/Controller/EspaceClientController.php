<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Comptes;
use App\Entity\Demandes;
use App\Entity\Operations;
use App\Form\ClientEditsProfileType;
use App\Form\ClientMakesOperationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EspaceClientController extends AbstractController
{
    private $em;
    private $request;
    private $appKernel;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack, KernelInterface $appKernel, Profiler $profiler)
    {
        $request = $requestStack->getCurrentRequest();
        $this->em = $em;
        $this->request = $request;
        $this->appKernel = $appKernel;
        $this->profiler = $profiler;
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
        dump($client);

        $comptes = $client->getComptes();
        dump($comptes);

        return $this->render('espace_client/index.html.twig', [
            'controller_name' => 'EspaceClientController',
            'client' => $client,
            'comptes' => $comptes,
        ]);
    }

    public function detail_compte($compte_id)
    {
        $client_user = $this->getUser();
        $client_id = $client_user->getClientId();
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id)
        ;
        $comptes = $client->getComptes();
        foreach ($comptes as $compte)
        {
            if ($compte->getId() == $compte_id) //User is owner of the account
            {
                $operations = $this->getDoctrine()
                    ->getRepository(Operations::class)
                    ->findByCompte($compte);
                dump($operations);

                return $this->render('espace_client/comptes/details.html.twig', [
                    'controller_name' => 'EspaceClientController',
                    'client' => $client,
                    'compte' => $compte,
                    'operations' => $operations,
                ]);
                break;
            }
        }

        return $this->redirectToRoute('clients_logout');
    }

    public function new_virement(EntityManagerInterface $em, Request $request)
    {
        $client_user = $this->getUser();
        $client_id = $client_user->getClientId();
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id)
        ;

        $form = $this->createForm(ClientMakesOperationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var $operation Operations
             */
            $operation = $form->getData();
            //dd($operation);
            $operation->setTypeOperation(Operations::TYPE_VIREMENT);
            $operation->setDateExecution( new \DateTime() );
            if ($operation_result = $operation->execute($em))
            {
                $this->addFlash('success', 'Votre opération a bien été enregistrée');
            }
            else
            {
                $this->addFlash('error', 'Votre opération n\'a pas pu être enregistrée. Vérifiez le solde de votre compte');
            }
            $em->persist($operation);
            $em->flush();

            return $this->redirectToRoute('clients_virement_new');
            /*unset($form);
            $form = $this->createForm(ClientMakesOperationType::class);

            return $this->render('espace_client/virements/nouveau.html.twig', [
                'operation_result' => $operation_result,
                'controller_name' => 'EspaceClientController',
                'client' => $client,
                'operation' => $operation,
                'client_virement_form' => $form->createView(),
            ]);*/
        }

        return $this->render('espace_client/virements/nouveau.html.twig', [
            'controller_name' => 'EspaceClientController',
            'client' => $client,
            'client_virement_form' => $form->createView(),
        ]);
    }

    public function new_beneficiaire()
    {
        $client_user = $this->getUser();
        $client_id = $client_user->getClientId();
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id)
        ;

        return $this->render('espace_client/virements/beneficiaires/nouveau.html.twig', [
            'controller_name' => 'EspaceClientController',
            'client' => $client,
        ]);
    }

    public function commander($article, $confirm)
    {
        $has_demande = $is_success = false;
        $client_user = $this->getUser();
        $client_id = $client_user->getClientId();
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id)
        ;

        dump($article);
        dump($confirm);

        $demande = new Demandes($article);
        dump($demande);

        if ($article == 'beneficiaire-virement')
        {
            $datas_request = $this->request->getContent();

            $beneficiaire = $this->request->get('beneficiaire');
            $code_bank = str_pad($this->request->get('code_bank'),5, '0', STR_PAD_LEFT);
            $code_agence = str_pad($this->request->get('code_agence'), 5, '0', STR_PAD_LEFT);
            $num_compte = str_pad($this->request->get('num_compte'), 11, '0', STR_PAD_LEFT);
            $cle_rib = str_pad($this->request->get('cle_rib'), 2, '0', STR_PAD_LEFT);
            $iban = 'FR75'.$code_bank.$code_agence.$num_compte.$cle_rib;

            if ($beneficiaire && $code_agence && $code_bank && $cle_rib && $iban)
            {
                $datas = [
                    'beneficiaire' => $beneficiaire,
                    'code_bank' => $code_bank,
                    'code_agence' => $code_agence,
                    'num_compte' => $num_compte,
                    'cle_rib' => $cle_rib,
                    'iban' => $iban,
                ];
                $demande->setDetails($datas);
                dump($datas);

                //Pas de demande existante, on enregistre la demande
                $client->addDemande($demande);
                $this->em->persist($client);
                $this->em->persist($demande);
                $this->em->flush();
                $this->addFlash('success', 'Nous avons bien enregistré votre demande.');
                $is_success = $has_demande = true;

                return $this->redirectToRoute('clients_demander', ['article' => $article]);
            }
        }


        //Check si une demande a déjà été faite
        $existing_demandes = $this->getDoctrine()
            ->getRepository(Demandes::class)
            ->findByTypeByClient($demande->getType(), $client->getId());

        if (count($existing_demandes))
        {
            $has_demande = true;
        }
        dump($existing_demandes);

        if ($confirm == true && !$has_demande && $article != 'beneficiaire-virement')
        {
            //Pas de demande existante, on enregistre la demande
            $client->addDemande($demande);
            $this->em->persist($client);
            $this->em->persist($demande);
            $this->em->flush();
            $this->addFlash('success', 'Nous avons bien enregistré votre demande.');
            $is_success = $has_demande = true;
        }
        elseif ($confirm == true) //should not happen normaly
        {
            $this->addFlash('warning', 'Vous avez déjà une demande enregistrée. Nous vous recontacterons prochainement.');
        }

        return $this->render('espace_client/commander/commande.html.twig', [
            'controller_name' => 'EspaceClientController',
            'client' => $client,
            'is_success' => $is_success,
            'has_demande' => $has_demande,
            'demande' => $demande,
            '_article' => $article,
            'confirm' => $confirm,
        ]);
    }

    public function profile(EntityManagerInterface $em)
    {
        $request = $this->request;

        $client_user = $this->getUser();
        $client_id = $client_user->getClientId();
        $client = $this->getDoctrine()
            ->getRepository(Client::class)
            ->find($client_id)
        ;

        $form = $this->createForm(ClientEditsProfileType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var $client_form Client
             */
            $client_form = $form->getData();
            //dd($operation);
            $em->persist($client_form);
            $em->flush();

            $this->addFlash('success', 'Vos informations ont bien été enregistrées.');

            return $this->redirectToRoute('clients_profile');
        }

        return $this->render('espace_client/profile/profile.html.twig', [
            'controller_name' => 'EspaceClientController',
            'client' => $client,
            'client_profile_form' => $form->createView(),
        ]);
    }

    public function auth_pad()
    {
        $session = $this->get('session');
        //dump($session);
        $tab = $pad_tab = $session->get('pad_tab');
        $cellwidth = $session->get('pad_tab_cell_width');

        //dump($pad_tab);
        //dd($session);

        $tab2 = array();
        $width = $height = ceil(sqrt(count($tab)))*$cellwidth;
        /*error_log('count($tab) : ' . count($tab));
        error_log('sqrt(count($tab)) : ' . sqrt(count($tab)));
        error_log('ceil(sqrt(count($tab))) : ' . ceil(sqrt(count($tab))));*/

        $image = imagecreatetruecolor($width,$height);

        $white = imagecolorallocate($image, 255, 255, 255);
        $grey = imagecolorallocate($image, 128, 128, 128);
        $black = imagecolorallocate($image, 0, 0, 0);
        $red = imagecolorallocate($image, 255, 0, 0);
        $blue = imagecolorallocate($image, 0, 0, 255);
        imagefilledrectangle($image, 0, 0, $width-1, $height-1, $white);

        $font = $this->appKernel->getProjectDir();
        $font .= '/public/img/arial.ttf';
        foreach ($tab as $i => $val)
        {
            if ($val)
            {
                $tab2[$i] = hash('sha256', $val);
            }
            if ($i%($width/$cellwidth) == 0)
            {
                $x = 9 + $cellwidth*(floor($i/($width/$cellwidth)));
            }
            $y = 22 + $cellwidth*($i%($width/$cellwidth));
            imagettftext($image, 16, 0, $x+4, $y+4, $grey, $font, $tab[$i]);
            imagettftext($image, 16, 0, $x-2, $y-2, $blue, $font, $tab[$i]);
        }
        //error_log('$tab2 = '.print_r($tab2, true));

        for ($i=0;$i<($width/$cellwidth - 1);$i++)
        {
            imageline ($image, 0, $cellwidth + $cellwidth*$i, $width, $cellwidth + $cellwidth*$i, $black);
            imageline ($image, $cellwidth * ( 1 + $i), 0, $cellwidth * (1 + $i), $height, $black);
        }

        $image_name = 'clavier.png';
        $headers = array(
            'Content-Type'     => 'image/png',
            'Content-Disposition' => 'inline; filename="'.$image_name.'"');
        //$this->profiler->disable();

        ob_start();
        imagepng($image);
        $str_img = ob_get_contents();
        ob_end_clean();

        imagedestroy($image);
        return new Response($str_img, 200, $headers);
    }
}
