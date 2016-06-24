<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\ContactDiplomeType;
use AppBundle\Form\DiplomeType;
use AppBundle\Entity\ContactDiplome;
use AppBundle\Entity\Diplome;
use AppBundle\Entity\Suivi;


class DiplomeController extends Controller
{


	/**
	* @Route("/admin/diplomes", name="list_admin_diplomes")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listDiplomesAction()
	{

		$this->get('app.security')->checkAccess('DIPLOME_READ');

	  	$diplomes = $this->getDoctrine()
	  					->getRepository('AppBundle:Diplome')
	  					->findAll();

	  	$diplomeForms = array();

	  	foreach ($diplomes as $diplome) {
	  		$diplomeForm = $this->createForm(DiplomeType::class, $diplome,array(
	  				'action'=> $this->generateUrl('save_diplome').'?idDiplome='.$diplome->getId(),
	  			));
	  		$diplomeForms[$diplome->getId()] = $diplomeForm->createView();
	  	}

	  	$newDiplomeForm = $this->createForm(DiplomeType::class, new Diplome() ,array(
				'action'=> $this->generateUrl('save_diplome'),
			));

	    return $this->render('admin/diplomes.html.twig',
	    	[
	    		'diplomes' => $diplomes,
	    		'diplomeForms' => $diplomeForms,
	    		'newDiplomeForm' => $newDiplomeForm->createView(),
	    	]);
	}

	/**
	* @Route("/diplome/save", name="save_diplome")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function saveDiplomeAction(Request $request)
	{

		$this->get('app.security')->checkAccess('DIPLOME_WRITE');

		if($request->query->get('idDiplome')){
			$diplome = $this->getDoctrine()
				->getRepository('AppBundle:Diplome')
				->find($request->query->get('idDiplome'));
				$add = false;
		}else{
			$diplome = new Diplome();
			$add = true;
		}

		$diplomeForm = $this->createForm(DiplomeType::class, $diplome);
		$diplomeForm->handleRequest($request);

		if ($diplomeForm->isSubmitted() && $diplomeForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($diplome);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($diplomeForm->isSubmitted() && !$diplomeForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($diplomeForm);
		}

		return $this->redirectToRoute('list_admin_diplomes');
	}


	/**
	* @Route("/diplome/delete/{idDiplome}", name="delete_diplome")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function deleteDiplomeAction($idDiplome)
	{

		$this->get('app.security')->checkAccess('DIPLOME_DELETE');

	    $diplome = $this->getDoctrine()
	      ->getRepository('AppBundle:Diplome')
	      ->find($idDiplome);

	    $contactsDiplome = $this->getDoctrine()
	    	->getRepository('AppBundle:ContactDiplome')
	    	->findBy(array('diplome'=>$diplome));

	    if(sizeof($contactsDiplome)>0){
			$this->get('session')->getFlashBag()->add('danger', 'Suppression impossible : ce diplome est attribué à '.sizeof($contactsDiplome).' contact(s)');
	    	
	    	return $this->redirectToRoute('list_admin_diplomes');
	    }

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($diplome);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_admin_diplomes');
	}


	/**
	* @Route("/contact/{idContact}/diplomes", name="list_diplomes")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function listContactDiplomesAction($idContact)
	{

		$this->get('app.security')->checkAccess('DIPLOME_READ');

	  	$contact = $this->getDoctrine()
	  					->getRepository('AppBundle:Contact')
	  					->find($idContact);

	  	$contactDiplomes = $this->getDoctrine()
	  					->getRepository('AppBundle:ContactDiplome')
	  					->findBy(array('contact'=>$contact),array('dateObtention'=>'ASC'));

	  	$cdForms = array();

	  	foreach ($contactDiplomes as $cd) {
	  		$contactDiplomeForm = $this->createForm(ContactDiplomeType::class, $cd,array(
	  				'action'=> $this->generateUrl('save_contact_diplome').'?idContact='.$contact->getId().'&idContactDiplome='.$cd->getId(),
	  			));
	  		$cdForms[$cd->getId()] = $contactDiplomeForm->createView();
	  	}

	  	$newContactDiplome = new ContactDiplome();
	  	$newContactDiplome->setDateObtention(new \DateTime());

	  	$nextAg = false;
	  	if($contact->getSection()){
		  	$nextAg = $this->getDoctrine()
		  		->getRepository('AppBundle:AssembleeGenerale')
		  		->findNextBySection($contact->getSection());
	  	}

	  	if($nextAg){
	  		$newContactDiplome->setDateObtention($nextAg->getDate());
	  	}

	  	$newContactDiplomeForm = $this->createForm(ContactDiplomeType::class, $newContactDiplome ,array(
				'action'=> $this->generateUrl('save_contact_diplome').'?idContact='.$contact->getId(),
			));

	    return $this->render('operateur/contacts/diplomes.html.twig',
	    	[
	    		'contact' => $contact,
	    		'contactDiplomes' => $contactDiplomes,
	    		'cdForms' => $cdForms,
	    		'newContactDiplomeForm' => $newContactDiplomeForm->createView(),
	    	]);
	}

	/**
	* @Route("/contact-diplome/save", name="save_contact_diplome")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function saveContactDiplomeAction(Request $request)
	{

		$this->get('app.security')->checkAccess('DIPLOME_WRITE');

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idContactDiplome')){
			$contactDiplome = $this->getDoctrine()
				->getRepository('AppBundle:ContactDiplome')
				->find($request->query->get('idContactDiplome'));
		}else{
			$contactDiplome = new ContactDiplome();
			$contactDiplome->setContact($contact);
		}

		$contactDiplomeForm = $this->createForm(ContactDiplomeType::class, $contactDiplome);
		$contactDiplomeForm->handleRequest($request);

		if ($contactDiplomeForm->isSubmitted() && $contactDiplomeForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($contactDiplome);
			$em->flush();

			$suivi = new Suivi();
			$suivi->setContact($contact)
				->setOperateur($this->getUser())
				->setIsOk(true)
				->setDateCreation(new \DateTime())
				->setTexte('Ajout d\'un diplome');

			$em->persist($suivi);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($contactDiplomeForm->isSubmitted() && !$contactDiplomeForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($contactDiplomeForm);
		}

		return $this->redirectToRoute('list_diplomes',array('idContact'=>$contact->getId()));
	}


	/**
	* @Route("/contact-diplome/delete/{idContactDiplome}", name="delete_contact_diplome")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function deleteContactDiplomeAction($idContactDiplome)
	{

		$this->get('app.security')->checkAccess('DIPLOME_DELETE');

	    $cd = $this->getDoctrine()
	      ->getRepository('AppBundle:ContactDiplome')
	      ->find($idContactDiplome);
		
		$contact = $cd->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($cd);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_diplomes',array('idContact'=>$contact->getId()));
	}

}