<?php
// src/BayardTest/UserBundle/Controller/SecurityController.php;

namespace BayardTest\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BayardTest\UserBundle\Entity\User;

use BayardTest\UserBundle\Form\UserType;


class SecurityController extends Controller
{
	public function loginAction(Request $request)
	{
		// Si le visiteur est déjà identifié, on le redirige vers l'accueil
		if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			return $this->redirectToRoute('bayardtest_platform_view');
		}

		// Le service authentication_utils permet de récupérer le nom d'utilisateur
		// et l'erreur dans le cas où le formulaire a déjà été soumis mais était invalide
		// (mauvais mot de passe par exemple)
		$authenticationUtils = $this->get('security.authentication_utils');

		return $this->render('BayardTestUserBundle:Security:login.html.twig', array(
		'last_username' => $authenticationUtils->getLastUsername(),
		'error'         => $authenticationUtils->getLastAuthenticationError(),
		));
	}

	public function sign_upAction(Request $request)
	{
		$user = new User();
		$user->setSalt('');

		$form = $this->createForm(UserType::class, $user);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            return $this->redirectToRoute('login');
        }

        return $this->render('@BayardTestUser/Security/sign_up.html.twig', array(
            'form' => $form->createView(),
        ));
	}
}
