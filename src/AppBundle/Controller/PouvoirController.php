<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\PouvoirType;
use AppBundle\Entity\Pouvoir;


class PouvoirController extends Controller
{

	/**
	* @Route("/contact/{idContact}/pouvoirs", name="list_pouvoirs")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function listPouvoirsAction($idContact)
	{

		$this->get('app.security')->checkAccess('POUVOIR_READ');

	  	$contact = $this->getDoctrine()
	  					->getRepository('AppBundle:Contact')
	  					->find($idContact);

	  	$pouvoirs = $this->getDoctrine()
	  					->getRepository('AppBundle:Pouvoir')
	  					->findBy(array('contact'=>$contact),array('date'=>'ASC'));

	  	$pouvoirForms = array();

	  	foreach ($pouvoirs as $pouvoir) {
	  		$pouvoirForm = $this->createForm(PouvoirType::class, $pouvoir,array(
	  				'action'=> $this->generateUrl('save_pouvoir').'?idContact='.$contact->getId().'&idPouvoir='.$pouvoir->getId(),
	  			));
	  		$pouvoirForms[$pouvoir->getId()] = $pouvoirForm->createView();
	  	}

	  	$newPouvoirForm = $this->createForm(PouvoirType::class, new Pouvoir() ,array(
				'action'=> $this->generateUrl('save_pouvoir').'?idContact='.$contact->getId(),
			));

	    return $this->render('operateur/contacts/pouvoirs.html.twig',
	    	[
	    		'contact' => $contact,
	    		'pouvoirs' => $pouvoirs,
	    		'pouvoirForms' => $pouvoirForms,
	    		'newPouvoirForm' => $newPouvoirForm->createView(),
	    	]);
	}

	/**
	* @Route("/pouvoir/save", name="save_pouvoir")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function savePouvoirAction(Request $request)
	{

		$this->get('app.security')->checkAccess('POUVOIR_WRITE');

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idPouvoir')){
			$pouvoir = $this->getDoctrine()
				->getRepository('AppBundle:Pouvoir')
				->find($request->query->get('idPouvoir'));
				$add = false;
		}else{
			$pouvoir = new Pouvoir();
			$pouvoir->setContact($contact);
			$add = true;
		}

		$pouvoirForm = $this->createForm(PouvoirType::class, $pouvoir);
		$pouvoirForm->handleRequest($request);

		if ($pouvoirForm->isSubmitted() && $pouvoirForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($pouvoir);
			$em->flush();

			if($add){
				$this->get('app.suivi')->create($contact,"Ajout d'un pouvoir");
			}

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($pouvoirForm->isSubmitted() && !$pouvoirForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($pouvoirForm);
		}

		return $this->redirectToRoute('list_pouvoirs',array('idContact'=>$contact->getId()));
	}


	/**
	* @Route("/pouvoir/delete/{idPouvoir}", name="delete_pouvoir")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function deletePouvoirAction($idPouvoir)
	{

		$this->get('app.security')->checkAccess('POUVOIR_DELETE');

	    $pouvoir = $this->getDoctrine()
	      ->getRepository('AppBundle:Pouvoir')
	      ->find($idPouvoir);
		
		$contact = $pouvoir->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($pouvoir);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_pouvoirs',array('idContact'=>$contact->getId()));
	}

}