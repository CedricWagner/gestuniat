<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\DonType;
use AppBundle\Entity\Don;


class DonController extends Controller
{

	/**
	* @Route("/contact/{idContact}/dons", name="list_dons")
	* @Security("has_role('ROLE_USER')")
	*/
	public function listDonsAction($idContact)
	{
	  	$contact = $this->getDoctrine()
	  					->getRepository('AppBundle:Contact')
	  					->find($idContact);

	  	$dons = $this->getDoctrine()
	  					->getRepository('AppBundle:Don')
	  					->findBy(array('contact'=>$contact),array('date'=>'ASC'));

	  	$donForms = array();

	  	foreach ($dons as $don) {
	  		$donForm = $this->createForm(DonType::class, $don,array(
	  				'action'=> $this->generateUrl('save_don').'?idContact='.$contact->getId().'&idDon='.$don->getId(),
	  			));
	  		$donForms[$don->getId()] = $donForm->createView();
	  	}

	  	$newDonForm = $this->createForm(DonType::class, new Don() ,array(
			'action'=> $this->generateUrl('save_don').'?idContact='.$contact->getId(),
		));

	    return $this->render('operateur/contacts/dons.html.twig',
	    	[
	    		'contact' => $contact,
	    		'dons' => $dons,
	    		'donForms' => $donForms,
	    		'newDonForm' => $newDonForm->createView(),
	    	]);
	}

	/**
	* @Route("/don/save", name="save_don")
	* @Security("has_role('ROLE_USER')")
	*/
	public function saveDonAction(Request $request)
	{

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idDon')){
			$don = $this->getDoctrine()
				->getRepository('AppBundle:Don')
				->find($request->query->get('idDon'));
		}else{
			$don = new Don();
			$don->setContact($contact);
			$don->setOperateur($this->getUser());
		}

		$donForm = $this->createForm(DonType::class, $don);
		$donForm->handleRequest($request);

		if ($donForm->isSubmitted() && $donForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($don);
			$em->flush();
		}

		return $this->redirectToRoute('list_dons',array('idContact'=>$contact->getId()));
	}


	/**
	* @Route("/don/delete/{idDon}", name="delete_don")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteDonAction($idDon)
	{

	    $don = $this->getDoctrine()
	      ->getRepository('AppBundle:Don')
	      ->find($idDon);
		
		$contact = $don->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($don);
		$em->flush();

	    return $this->redirectToRoute('list_dons',array('idContact'=>$contact->getId()));
	}

}