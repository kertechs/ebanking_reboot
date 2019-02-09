<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ClientsSecurityController extends AbstractController
{
    /**
     * @Route(
     *     "/login",
     *     name="clients_login",
     *     host="{domain}",
     *     defaults={"domain"="clients.banquedauphine.online"},
     *     requirements={"domain"="clients.banquedauphine.online"}
     * )
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request, $domain): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        //dd($domain);

        $tab = array (
            1,2,3,4,5,6,7,8,9,0,
            'p','a','s','w','o','r','d',
            'x','y','z','w','v','u','q','b',
            '','','','','','','','','',''
        );

        shuffle($tab);
        $session = $this->get('session');
        //dump($session);
        $session->set('pad_tab', $tab);

        $cellwidth = 30;
        $session->set('pad_tab_cell_width', $cellwidth);

        $width = $height = ceil(sqrt(count($tab)))*$cellwidth;

        return $this->render( '/security/'.$domain.'/login.html.twig', [
            'last_username' => $lastUsername,
            'pad_tab' => $tab,
            'pad_tab_cell_width' => $cellwidth,
            'pad_tab_width' => $width,
            'pad_tab_height' => $height,
            'error' => $error
        ]);
    }
}
