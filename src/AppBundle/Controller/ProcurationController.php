<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\ProcurationType;
use AppBundle\Entity\Procuration;


class ProcurationController extends Controller
{

	/**
	* @Route("/contact/{idContact}/procurations", name="list_procurations")
	* @Security("has_role('ROLE_USER')")
	*/
	public function listProcurationsAction($idContact)
	{
	  	$contact = $this->getDoctrine()
	  					->getRepository('AppBundle:Contact')
	  					->find($idContact);

	  	$procurations = $this->getDoctrine()
	  					->getRepository('AppBundle:Procuration')
	  					->findBy(array('contact'=>$contact),array('date'=>'ASC'));

	  	$procurationForms = array();

	  	foreach ($procurations as $procuration) {
	  		$procurationForm = $this->createForm(ProcurationType::class, $procuration,array(
	  				'action'=> $this->generateUrl('save_procuration').'?idContact='.$contact->getId().'&idProcuration='.$procuration->getId(),
	  			));
	  		$procurationForms[$procuration->getId()] = $procurationForm->createView();
	  	}

	  	$newProcurationForm = $this->createForm(ProcurationType::class, new Procuration() ,array(
				'action'=> $this->generateUrl('save_procuration').'?idContact='.$contact->getId(),
			));

	    return $this->render('operateur/contacts/procurations.html.twig',
	    	[
	    		'contact' => $contact,
	    		'procurations' => $procurations,
	    		'procurationForms' => $procurationForms,
	    		'newProcurationForm' => $newProcurationForm->createView(),
	    	]);
	}

	/**
	* @Route("/procuration/save", name="save_procuration")
	* @Security("has_role('ROLE_USER')")
	*/
	public function saveProcurationAction(Request $request)
	{

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idProcuration')){
			$procuration = $this->getDoctrine()
				->getRepository('AppBundle:Procuration')
				->find($request->query->get('idProcuration'));
		}else{
			$procuration = new Procuration();
			$procuration->setContact($contact);
		}

		$procurationForm = $this->createForm(ProcurationType::class, $procuration);
		$procurationForm->handleRequest($request);

		if ($procurationForm->isSubmitted() && $procurationForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($procuration);
			$em->flush();
		}

		return $this->redirectToRoute('list_procurations',array('idContact'=>$contact->getId()));
	}


	/**
	* @Route("/procuration/delete/{idProcuration}", name="delete_procuration")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteProcurationAction($idProcuration)
	{

	    $procuration = $this->getDoctrine()
	      ->getRepository('AppBundle:Procuration')
	      ->find($idProcuration);
		
		$contact = $procuration->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($procuration);
		$em->flush();

	    return $this->redirectToRoute('list_procurations',array('idContact'=>$contact->getId()));
	}

}