<?php

namespace BayardTest\PlatformBundle\Controller;

use BayardTest\PlatformBundle\Entity\Advert;
use BayardTest\PlatformBundle\Entity\Image;
use BayardTest\PlatformBundle\Entity\Application;
use BayardTest\PlatformBundle\Entity\Category;
use BayardTest\PlatformBundle\Entity\Skill;
use BayardTest\PlatformBundle\Entity\AdvertSkill;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use BayardTest\PlatformBundle\Form\AdvertType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
     * @Route("/platform")
     */
class DefaultController extends Controller
{

    /**
     * @Route("/", name="bayardtest_platform_home")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        /*// On vérifie que l'utilisateur dispose bien du rôle ROLE_ADMIN
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
          // Sinon on déclenche une exception « Accès interdit »
          throw new AccessDeniedException('Accès limité à l\'admin.');
        }

        // Ici l'utilisateur a les droits suffisant,*/

        /*$url = array('year'   => 2012,
                     'slug'   => 'hello',
                     'format' => 'html');*/
        $url = $this->get('router')
                    ->generate(
                        'bayardtest_platform_view_slug',
                        array(
                            'year'   => 2012,
                                'slug'   => 'hello',
                                'format' => 'html'
                            ),
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
        return $this->render('@BayardTestPlatform/Default/index.html.twig', array('url' => $url));
    }

    /**
     * @Route("/view", name="bayardtest_platform_view")
     */
    public function viewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $adverts = $em->getRepository('BayardTestPlatformBundle:Advert')->findAll();

        if (null === $adverts) {
            throw new NotFoundHttpException("la table application est vide");
        }

        return $this->render('@BayardTestPlatform/Default/view.html.twig', array('adverts' => $adverts));
    }

    /**
     * @Route("/view/images", name="bayardtest_platform_view_images")
     */
    public function viewImagesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $images = $em->getRepository('BayardTestPlatformBundle:Image')->findAll();

        if (null === $images) {
            throw new NotFoundHttpException("la table application est vide");
        }

        return $this->render('@BayardTestPlatform/Default/viewImages.html.twig', array('images' => $images));
    }

    /**
     * @Route("/add", name="bayardtest_platform_add")
     * @Security("has_role('ROLE_AUTEUR')")
     */
    public function addAction(Request $request)
    {
        /*// On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
            // Sinon on déclenche une exception « Accès interdit »
            throw new AccessDeniedException('Accès limité aux auteurs.');
        }*/

        $advert = new Advert();

        $form = $this->createForm(AdvertType::class, $advert);
        // $form = $this->get('form.factory')->create(AdvertType::class, $advert);


        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            return $this->redirectToRoute('bayardtest_platform_view');
        }

        return $this->render('@BayardTestPlatform/Default/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/addAuto", name="bayardtest_platform_add_auto")
     */
    public function addAutoAction(Request $request)
    {
        // Création de l'entité Advert
        $advert = new Advert();
        $advert->setTitle('Recherche développeur Symfony.');
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");
        // Création de l'entité Image
        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');
        // On lie l'image à l'annonce
        $advert->setImage($image);
        // Création d'une première candidature
        $application1 = new Application();
        $application1->setAuthor('Marine');
        $application1->setContent("J'ai toutes les qualités requises.");
        $application1->setDate(new \DateTime());
        // Création d'une deuxième candidature par exemple
        $application2 = new Application();
        $application2->setAuthor('Pierre');
        $application2->setContent("Je suis très motivé.");
        $application2->setDate(new \DateTime());
        // On lie les candidatures à l'annonce
        $advert->addApplication($application1);
        $advert->addApplication($application2);
        $category1 = new Category();
        $category1->setName("Web");
        $category2 = new Category();
        $category2->setName("BD");
        // On boucle sur les catégories pour les lier à l'annonce
        $advert->addCategory($category1);
        $advert->addCategory($category2);
        // // Liste des noms de compétences à ajouter
        // $names = array('PHP', 'Symfony', 'C++', 'Java', 'Photoshop', 'Blender', 'Bloc-note');
        // // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        // foreach ($names as $name) {
        //     // On crée la compétence
        //     $skill = new Skill();
        //     $skill->setName($name);
        //     // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
        //     $advertSkill = new AdvertSkill();
        //     // On la lie à l'annonce, qui est ici toujours la même
        //     $advertSkill->setAdvert($advert);
        //     // On la lie à la compétence, qui change ici dans la boucle foreach
        //     $advertSkill->setSkill($skill);
        //     // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
        //     $advertSkill->setLevel('Expert');
        //     // On la persiste
        //     $em->persist($skill);
        //     // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
        //     $em->persist($advertSkill);
        // }
        // Étape 1 : On « persiste » l'entité
        $em->persist($advert);
        // Étape 1 bis : si on n'avait pas défini le cascade={"persist"},
        // on devrait persister à la main l'entité $image
        // $em->persist($image);
        // Étape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
        // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
        $em->persist($application1);
        $em->persist($application2);
        $em->persist($category1);
        $em->persist($category2);
        // Étape 2 : On déclenche l'enregistrement
        $em->flush();
        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->redirectToRoute('bayardtest_platform_view');
    }

    /**
     * @Route("/remove/", name="bayardtest_platform_remove")
     */
    public function removeAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('BayardTestPlatformBundle:Advert')->findByAuthor("Alexandre");
        foreach ($advert as $tmp) {
            $em->remove($tmp);
        }
        $em->flush();

        return $this->redirectToRoute('bayardtest_platform_view');
    }

    /**
     * @Route("/remove/advert/{id}", name="bayardtest_platform_remove_advert")
     */
    public function removeAdvertAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('BayardTestPlatformBundle:Advert')->find($id);
        $em->remove($advert);
        $em->flush();

        return $this->redirectToRoute('bayardtest_platform_view');
    }

    /**
     * @Route("/remove/image/adverts/{id}", name="bayardtest_platform_remove_image_adverts")
     */
    public function removeImageByAdvertsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $adverts = $em->getRepository('BayardTestPlatformBundle:Advert')->findByImage($id);
        foreach ($adverts as $advert) {
            $advert->setImage(null);
        }
        $image = $em->getRepository('BayardTestPlatformBundle:Image')->find($id);
        $em->remove($image);
        
        $em->flush();

        return $this->redirectToRoute('bayardtest_platform_view');
    }

    /**
     * @Route("/remove/image/{id}", name="bayardtest_platform_remove_image")
     */
    public function removeImageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('BayardTestPlatformBundle:Image')->find($id);
        $em->remove($advert);
        $em->flush();

        return $this->redirectToRoute('bayardtest_platform_view');
    }


    ////////////////////////////Va prendre en comptre le "_" de format et va faire comptre au Kernel le formar que l'on donne
    // /**
    //  * @Route("/{year}/{slug}.{_format}", name="bayardtest_platform_view_slug", requirements={"year"="\d{4}", "format"="html|xml"})
    //  */
    // public function slugAction(Request $request, $year, $slug, $_format)
    // {
    // 	return $this->render('@BayardTestPlatform/Default/slug.html.twig', ['year' => $year, 'slug' => $slug, 'format' => $_format]);
    // }

    /**
     * @Route("/{year}/{slug}.{format}", name="bayardtest_platform_view_slug", requirements={"year"="\d{4}", "format"="html|xml"})
     */
    public function slugAction(Request $request, $year, $slug, $format)
    {
        return $this->render('@BayardTestPlatform/Default/slug.html.twig', ['year' => $year, 'slug' => $slug, 'format' => $format]);
    }


    /**
     * @Route("/redirect/index", name="bayardtest_platform_redirect_index")
     */
    public function redirectIndexAction(Request $request)
    {
        $url = $this->get('router')->generate('bayardtest_platform_home');
        return new RedirectResponse($url);
        //return $this->redirect($url);
        //return $this->redirectToRoute('bayardtest_platform_home');
    }

    /**
     * @Route("/menu", name="bayardtest_platform_menu")
     */
    public function menuAction(Request $request)
    {
        // On fixe en dur une liste ici, bien entendu par la suite
        // on la récupérera depuis la BDD !
        $listAdverts = array(
            array('id' => 2, 'title' => 'Recherche développeur Symfony'),
            array('id' => 5, 'title' => 'Mission de webmaster'),
            array('id' => 9, 'title' => 'Offre de stage webdesigner')
        );

        return $this->render('@BayardTestPlatform/Default/menu.html.twig', array(
            // Tout l'intérêt est ici : le contrôleur passe
            // les variables nécessaires au template !
            'listAdverts' => $listAdverts
        ));
    }

    /**
     * @Route("/edit/{id}", name="bayardtest_platform_edit")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('BayardTestPlatformBundle:Advert')->find($id);

        $form = $this->createForm(AdvertType::class, $advert);
        // $form = $this->get('form.factory')->create(AdvertType::class, $advert);


        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

                return $this->redirectToRoute('bayardtest_platform_view', array('id' => $advert->getId()));
            }
        }

        return $this->render('@BayardTestPlatform/Default/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/remove/{id}", name="bayardtest_platform_remove")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('BayardTestPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // On boucle sur les catégories de l'annonce pour les supprimer
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // On déclenche la modification
        $em->flush();

        // ...
        return $this->redirectToRoute('bayardtest_platform_view');
    }

    /**
     * @Route("/advert/category/{cat}", name="bayardtest_platform_advert_category")
     */
    public function listAdvertWithCategoriesAction($cat)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $adverts = $em->getRepository('BayardTestPlatformBundle:Advert')->getAdvertWithCategories([$cat]);


        // … reste de la méthode
        return $this->render('@BayardTestPlatform/Default/advert_category.html.twig', array(
            'cat' => $cat,
            'adverts' => $adverts
        ));
    }

    /**
     * @Route("/application/advert/{limit}", name="bayardtest_platform_application_advert")
     */
    public function advertApplicationAction($limit)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $applications = $em->getRepository('BayardTestPlatformBundle:Application')->getApplicationsWithAdvert($limit);


        // … reste de la méthode
        return $this->render('@BayardTestPlatform/Default/application_advert.html.twig', array(
            'applications' => $applications
        ));
    }

    /**
     * @Route("/advert/update", name="bayardtest_platform_advert_update")
     */
    public function advertUpdateAction()
    {
        $em = $this->getDoctrine()->getManager();

        $adverts = $em->getRepository('BayardTestPlatformBundle:Advert')->findByAuthor('Alexandre');

        foreach ($adverts as $advert) {
            $advert->setTitle("Recherche développeur web");
            $em->persist($advert);
        }

        $em->flush();

        return $this->redirectToRoute('bayardtest_platform_view');
    }

    /**
     * @Route("/test/slug", name="bayardtest_platform_test_slug")
     */
    public function testSlugAction()
    {
        $advert = new Advert();
        $advert->setTitle("Recherche développeur !");
        $advert->setAuthor('Alexandre');
        $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");

        $image = new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');
        $advert->setImage($image);

        $em = $this->getDoctrine()->getManager();
        $em->persist($advert);
        $em->flush(); // C'est à ce moment qu'est généré le slug

        return new Response('Slug généré : '.$advert->getSlug());
        // Affiche « Slug généré : recherche-developpeur »
    }
}
