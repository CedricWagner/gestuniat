<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\FonctionSectionType;
use AppBundle\Entity\FonctionSection;


class FonctionSectionController extends Controller
{

	/**
	* @Route("/admin/fonctions-section", name="list_fonctionSections")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listFonctionSectionsAction()
	{

	  	$fonctionSections = $this->getDoctrine()
	  					->getRepository('AppBundle:FonctionSection')
	  					->findAll();

	  	$fonctionSectionForms = array();

	  	foreach ($fonctionSections as $fonctionSection) {
	  		$fonctionSectionForm = $this->createForm(FonctionSectionType::class, $fonctionSection,array(
	  				'action'=> $this->generateUrl('save_fonctionSection').'?idFonctionSection='.$fonctionSection->getId(),
	  			));
	  		$fonctionSectionForms[$fonctionSection->getId()] = $fonctionSectionForm->createView();
	  	}

	  	$newFonctionSectionForm = $this->createForm(FonctionSectionType::class, new FonctionSection() ,array(
				'action'=> $this->generateUrl('save_fonctionSection'),
			));

	    return $this->render('admin/fonctions-section.html.twig',
	    	[
	    		'fonctionSections' => $fonctionSections,
	    		'fonctionSectionForms' => $fonctionSectionForms,
	    		'newFonctionSectionForm' => $newFonctionSectionForm->createView(),
	    	]);
	}

	/**
	* @Route("/fonctionSection/save", name="save_fonctionSection")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function saveFonctionSectionAction(Request $request)
	{

		if($request->query->get('idFonctionSection')){
			$fonctionSection = $this->getDoctrine()
				->getRepository('AppBundle:FonctionSection')
				->find($request->query->get('idFonctionSection'));
				$add = false;
		}else{
			$fonctionSection = new FonctionSection();
			$add = true;
		}

		$fonctionSectionForm = $this->createForm(FonctionSectionType::class, $fonctionSection);
		$fonctionSectionForm->handleRequest($request);

		if ($fonctionSectionForm->isSubmitted() && $fonctionSectionForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($fonctionSection);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($fonctionSectionForm->isSubmitted() && !$fonctionSectionForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($fonctionSectionForm);
		}

		return $this->redirectToRoute('list_fonctionSections');
	}


	/**
	* @Route("/fonctionSection/delete/{idFonctionSection}", name="delete_fonctionSection")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function deleteFonctionSectionAction($idFonctionSection)
	{

	    $fonctionSection = $this->getDoctrine()
	      ->getRepository('AppBundle:FonctionSection')
	      ->find($idFonctionSection);

	    $contactsFonction = $this->getDoctrine()
	    	->getRepository('AppBundle:Contact')
	    	->findBy(array('fonctionSection'=>$fonctionSection));

	    if(sizeof($contactsFonction)>0){
			$this->get('session')->getFlashBag()->add('danger', 'Suppression impossible : cette fonction est attribuée à '.sizeof($contactsFonction).' contact(s)');
	    	
	    	return $this->redirectToRoute('list_fonctionSections');
	    }

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($fonctionSection);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_fonctionSections');
	}

}