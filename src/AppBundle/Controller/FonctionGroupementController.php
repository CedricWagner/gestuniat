<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\FonctionGroupementType;
use AppBundle\Entity\FonctionGroupement;


class FonctionGroupementController extends Controller
{

	/**
	* @Route("/admin/fonctions-groupement", name="list_fonctionGroupements")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listFonctionGroupementsAction()
	{

		$this->get('app.security')->checkAccess('FONCTION_READ');

	  	$fonctionGroupements = $this->getDoctrine()
	  					->getRepository('AppBundle:FonctionGroupement')
	  					->findAll();

	  	$fonctionGroupementForms = array();

	  	foreach ($fonctionGroupements as $fonctionGroupement) {
	  		$fonctionGroupementForm = $this->createForm(FonctionGroupementType::class, $fonctionGroupement,array(
	  				'action'=> $this->generateUrl('save_fonctionGroupement').'?idFonctionGroupement='.$fonctionGroupement->getId(),
	  			));
	  		$fonctionGroupementForms[$fonctionGroupement->getId()] = $fonctionGroupementForm->createView();
	  	}

	  	$newFonctionGroupementForm = $this->createForm(FonctionGroupementType::class, new FonctionGroupement() ,array(
				'action'=> $this->generateUrl('save_fonctionGroupement'),
			));

	    return $this->render('admin/fonctions-groupement.html.twig',
	    	[
	    		'fonctionGroupements' => $fonctionGroupements,
	    		'fonctionGroupementForms' => $fonctionGroupementForms,
	    		'newFonctionGroupementForm' => $newFonctionGroupementForm->createView(),
	    	]);
	}

	/**
	* @Route("/fonctionGroupement/save", name="save_fonctionGroupement")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function saveFonctionGroupementAction(Request $request)
	{

		$this->get('app.security')->checkAccess('FONCTION_WRITE');

		if($request->query->get('idFonctionGroupement')){
			$fonctionGroupement = $this->getDoctrine()
				->getRepository('AppBundle:FonctionGroupement')
				->find($request->query->get('idFonctionGroupement'));
				$add = false;
		}else{
			$fonctionGroupement = new FonctionGroupement();
			$add = true;
		}

		$fonctionGroupementForm = $this->createForm(FonctionGroupementType::class, $fonctionGroupement);
		$fonctionGroupementForm->handleRequest($request);

		if ($fonctionGroupementForm->isSubmitted() && $fonctionGroupementForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($fonctionGroupement);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($fonctionGroupementForm->isSubmitted() && !$fonctionGroupementForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($fonctionGroupementForm);
		}

		return $this->redirectToRoute('list_fonctionGroupements');
	}


	/**
	* @Route("/fonctionGroupement/delete/{idFonctionGroupement}", name="delete_fonctionGroupement")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function deleteFonctionGroupementAction($idFonctionGroupement)
	{

		$this->get('app.security')->checkAccess('FONCTION_DELETE');

	    $fonctionGroupement = $this->getDoctrine()
	      ->getRepository('AppBundle:FonctionGroupement')
	      ->find($idFonctionGroupement);

	    $contactsFonction = $this->getDoctrine()
	    	->getRepository('AppBundle:Contact')
	    	->findBy(array('fonctionGroupement'=>$fonctionGroupement));

	    if(sizeof($contactsFonction)>0){
			$this->get('session')->getFlashBag()->add('danger', 'Suppression impossible : cette fonction est attribuée à '.sizeof($contactsFonction).' contact(s)');
	    	
	    	return $this->redirectToRoute('list_fonctionGroupements');
	    }

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($fonctionGroupement);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_fonctionGroupements');
	}

}