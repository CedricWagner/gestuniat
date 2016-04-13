<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\ContactDiplomeType;
use AppBundle\Entity\ContactDiplome;


class DiplomeController extends Controller
{

	/**
	* @Route("/contact/{idContact}/diplomes", name="list_diplomes")
	* @Security("has_role('ROLE_USER')")
	*/
	public function listDiplomesAction($idContact)
	{
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

	  	$newContactDiplomeForm = $this->createForm(ContactDiplomeType::class, new ContactDiplome() ,array(
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
	* @Security("has_role('ROLE_USER')")
	*/
	public function saveContactDiplomeAction(Request $request)
	{

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
		}

		return $this->redirectToRoute('list_diplomes',array('idContact'=>$contact->getId()));
	}


	/**
	* @Route("/contact-diplome/delete/{idContactDiplome}", name="delete_contact_diplome")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteContactDiplomeAction($idContactDiplome)
	{

	    $cd = $this->getDoctrine()
	      ->getRepository('AppBundle:ContactDiplome')
	      ->find($idContactDiplome);
		
		$contact = $cd->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($cd);
		$em->flush();

	    return $this->redirectToRoute('list_diplomes',array('idContact'=>$contact->getId()));
	}

}