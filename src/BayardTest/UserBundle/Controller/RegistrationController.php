<?php
// src/BayardTest/UserBundle/Controller/RegistrationController.php;

namespace BayardTest\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BayardTest\UserBundle\Entity\User;
use BayardTest\UserBundle\Form\UserType;

class RegistrationController extends Controller
{
    public function registrerAction(Request $request)
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

        return $this->render('@BayardTestUser/Security/registrer.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
