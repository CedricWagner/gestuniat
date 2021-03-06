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
use AppBundle\Entity\Suivi;


class DonController extends Controller
{

	/**
	* @Route("/contact/{idContact}/dons", name="list_dons")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function listDonsAction($idContact)
	{

		$this->get('app.security')->checkAccess('DON_READ');

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
	  		
	  		$tools = $this->get('app.tools');

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
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function saveDonAction(Request $request)
	{

		$this->get('app.security')->checkAccess('DON_WRITE');

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idDon')){
			$don = $this->getDoctrine()
				->getRepository('AppBundle:Don')
				->find($request->query->get('idDon'));
			$add = false;
		}else{
			$don = new Don();
			$don->setContact($contact);
			$don->setOperateur($this->getUser());
			$don->setDateSaisie(new \DateTime());
			$add = true;
		}

		$donForm = $this->createForm(DonType::class, $don);
		$donForm->handleRequest($request);

		if ($donForm->isSubmitted() && $donForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($don);
			$em->flush();

			if($add){
				$suivi = new Suivi();
				$suivi->setContact($contact)
					->setOperateur($this->getUser())
					->setDateCreation(new \DateTime())
					->setIsOk(true)
					->setTexte('Ajout d\'un nouveau don');
			
				$em->persist($suivi);
				$em->flush();

			}

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($donForm->isSubmitted() && !$donForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($donForm);
		}

		return $this->redirectToRoute('list_dons',array('idContact'=>$contact->getId()));
	}


	/**
	* @Route("/don/delete/{idDon}", name="delete_don")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function deleteDonAction($idDon)
	{

		$this->get('app.security')->checkAccess('DON_DELETE');

	    $don = $this->getDoctrine()
	      ->getRepository('AppBundle:Don')
	      ->find($idDon);
		
		$contact = $don->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($don);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_dons',array('idContact'=>$contact->getId()));
	}

}