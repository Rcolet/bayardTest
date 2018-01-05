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

        $applications = $em->getRepository('BayardTestPlatformBundle:Application')->findAll();

        if (null === $applications) {
            throw new NotFoundHttpException("la table application est vide");
        }

    	return $this->render('@BayardTestPlatform/Default/view.html.twig', array('applications' => $applications));
    }

    /**
     * @Route("/add", name="oc_platform_add")
     */
    public function addAction(Request $request)
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

        // Liste des noms de compétences à ajouter
        $names = array('PHP', 'Symfony', 'C++', 'Java', 'Photoshop', 'Blender', 'Bloc-note');

        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        foreach ($names as $name) {

            // On crée la compétence
            $skill = new Skill();
            $skill->setName($name);

            // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
            $advertSkill = new AdvertSkill();

            // On la lie à l'annonce, qui est ici toujours la même
            $advertSkill->setAdvert($advert);

            // On la lie à la compétence, qui change ici dans la boucle foreach
            $advertSkill->setSkill($skill);

            // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
            $advertSkill->setLevel('Expert');

            // On la persiste
            $em->persist($skill);

            // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
            $em->persist($advertSkill);
        }

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
        return $this->redirectToRoute('oc_platform_view');
    }

    /**
     * @Route("/remove", name="oc_platform_remove")
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

}
