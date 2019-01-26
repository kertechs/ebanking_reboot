<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class BankersSecurityController extends AbstractController
{
    /**
     * @Route(
     *     "/login",
     *     name="backoffice_login",
     *     host="{domain}",
     *     defaults={"domain"="backoffice.banquedauphine.services"},
     *     requirements={"domain"="backoffice.banquedauphine.services"}
     * )
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request, $domain): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        //dd($domain);
        return $this->render( '/security/'.$domain.'/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'context_entry' => 'Connexion',
        ]);
    }
}
