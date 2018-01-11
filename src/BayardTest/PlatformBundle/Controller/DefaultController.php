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


/**
     * @Route("/platform")
     */
class DefaultController extends Controller
{

	/**
     * @Route("/", name="oc_platform_home")
     */
    public function indexAction(Request $request)
    {
    	/*$url = array('year'   => 2012,
    				 'slug'   => 'hello',
    				 'format' => 'html');*/
    	$url = $this->get('router')->generate('oc_platform_view_slug', 
    		   array('year'   => 2012,
    				 'slug'   => 'hello',
    				 'format' => 'html')
    		   , UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->render('@BayardTestPlatform/Default/index.html.twig', array('url' => $url));
    }

    /**
     * @Route("/view", name="oc_platform_view")
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
     * @Route("/view/images", name="oc_platform_view_images")
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
     * @Route("/add", name="oc_platform_add")
     */
    public function addAction(Request $request)
    {   
        $advert = new Advert();

        $form = $this->createForm(AdvertType::class, $advert);
        // $form = $this->get('form.factory')->create(AdvertType::class, $advert);


        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

                return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
            }
        }

        return $this->render('@BayardTestPlatform/Default/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/remove/", name="oc_platform_remove")
     */
    public function removeAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('BayardTestPlatformBundle:Advert')->findByAuthor("Alexandre");
        foreach ($advert as  $tmp) {
            $em->remove($tmp);
        }
        $em->flush();

        return $this->redirectToRoute('oc_platform_view');
    }

    /**
     * @Route("/remove/advert/{id}", name="oc_platform_remove_advert")
     */
    public function removeAdvertAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('BayardTestPlatformBundle:Advert')->find($id);
        $em->remove($advert);
        $em->flush();

        return $this->redirectToRoute('oc_platform_view');
    }

    /**
     * @Route("/remove/image/adverts/{id}", name="oc_platform_remove_image_adverts")
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

        return $this->redirectToRoute('oc_platform_view');
    }

    /**
     * @Route("/remove/image/{id}", name="oc_platform_remove_image")
     */
    public function removeImageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('BayardTestPlatformBundle:Image')->find($id);
        $em->remove($advert);
        $em->flush();

        return $this->redirectToRoute('oc_platform_view');
    }


    ////////////////////////////Va prendre en comptre le "_" de format et va faire comptre au Kernel le formar que l'on donne
    // /**
    //  * @Route("/{year}/{slug}.{_format}", name="oc_platform_view_slug", requirements={"year"="\d{4}", "format"="html|xml"})
    //  */
    // public function slugAction(Request $request, $year, $slug, $_format)
    // {
    // 	return $this->render('@BayardTestPlatform/Default/slug.html.twig', ['year' => $year, 'slug' => $slug, 'format' => $_format]);
    // }

    /**
     * @Route("/{year}/{slug}.{format}", name="oc_platform_view_slug", requirements={"year"="\d{4}", "format"="html|xml"})
     */
    public function slugAction(Request $request, $year, $slug, $format)
    {
    	return $this->render('@BayardTestPlatform/Default/slug.html.twig', ['year' => $year, 'slug' => $slug, 'format' => $format]);
    }


    /**
     * @Route("/redirect/index", name="oc_platform_redirect_index")
     */
    public function redirectIndexAction(Request $request)
    {
		$url = $this->get('router')->generate('oc_platform_home');
    	return new RedirectResponse($url);
    	//return $this->redirect($url);
    	//return $this->redirectToRoute('oc_platform_home');
    }

    /**
     * @Route("/menu", name="oc_platform_menu")
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
     * @Route("/edit/{id}", name="oc_platform_edit")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('BayardTestPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }


        // La méthode findAll retourne toutes les catégories de la base de données
        $listCategories = $em->getRepository('BayardTestPlatformBundle:Category')->findAll();


        // On boucle sur les catégories pour les lier à l'annonce
        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // Étape 2 : On déclenche l'enregistrement
        $em->flush();

        // … reste de la méthode
        return $this->redirectToRoute('oc_platform_view');
    }

    /**
     * @Route("/remove/{id}", name="oc_platform_remove")
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
        return $this->redirectToRoute('oc_platform_view');
    }

    /**
     * @Route("/advert/category/{cat}", name="oc_platform_advert_category")
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
     * @Route("/application/advert/{limit}", name="oc_platform_application_advert")
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
     * @Route("/advert/update", name="oc_platform_advert_update")
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

        return $this->redirectToRoute('oc_platform_view');
    }

    /**
     * @Route("/test/slug", name="oc_platform_test_slug")
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
