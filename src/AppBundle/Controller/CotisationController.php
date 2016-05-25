<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\CotisationType;
use AppBundle\Entity\Cotisation;


class CotisationController extends Controller
{

	/**
	* @Route("/contact/{idContact}/cotisations", name="list_cotisations")
	* @Security("has_role('ROLE_USER')")
	*/
	public function listCotisationsAction($idContact)
	{
	  	$contact = $this->getDoctrine()
	  					->getRepository('AppBundle:Contact')
	  					->find($idContact);

	  	$cotisations = $this->getDoctrine()
	  					->getRepository('AppBundle:Cotisation')
	  					->findBy(array('contact'=>$contact),array('dateCreation'=>'ASC'));

	  	$cotisationForms = array();

	  	foreach ($cotisations as $cotisation) {
	  		$cotisationForm = $this->createForm(CotisationType::class, $cotisation,array(
	  				'action'=> $this->generateUrl('save_cotisation').'?idContact='.$contact->getId().'&idCotisation='.$cotisation->getId(),
	  			));
	  		$cotisationForms[$cotisation->getId()] = $cotisationForm->createView();
	  	}

	  	$paramCotis = $this->getDoctrine()
	  		->getRepository('AppBundle:Parametre')
	  		->findOneBy(array('code'=>'COTISATION_MONTANT_ANNUEL'));

	  	$newCotisation = new Cotisation();
	  	$newCotisation->setMontant($paramCotis->getValue());

	  	$newCotisationForm = $this->createForm(CotisationType::class, $newCotisation ,array(
				'action'=> $this->generateUrl('save_cotisation').'?idContact='.$contact->getId(),
			));

	    return $this->render('operateur/contacts/cotisations.html.twig',
	    	[
	    		'contact' => $contact,
	    		'cotisations' => $cotisations,
	    		'cotisationForms' => $cotisationForms,
	    		'newCotisationForm' => $newCotisationForm->createView(),
	    	]);
	}

	/**
	* @Route("/cotisation/save", name="save_cotisation")
	* @Security("has_role('ROLE_USER')")
	*/
	public function saveCotisationAction(Request $request)
	{

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idCotisation')){
			$cotisation = $this->getDoctrine()
				->getRepository('AppBundle:Cotisation')
				->find($request->query->get('idCotisation'));
		}else{
			$cotisation = new Cotisation();
			$cotisation->setContact($contact);
		}

		$cotisationForm = $this->createForm(CotisationType::class, $cotisation);
		$cotisationForm->handleRequest($request);

		if ($cotisationForm->isSubmitted() && $cotisationForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($cotisation);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($cotisationForm->isSubmitted() && !$cotisationForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($cotisationForm);
		}

		return $this->redirectToRoute('list_cotisations',array('idContact'=>$contact->getId()));
	}


	/**
	* @Route("/cotisation/delete/{idCotisation}", name="delete_cotisation")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteCotisationAction($idCotisation)
	{

	    $cotisation = $this->getDoctrine()
	      ->getRepository('AppBundle:Cotisation')
	      ->find($idCotisation);
		
		$contact = $cotisation->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($cotisation);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_cotisations',array('idContact'=>$contact->getId()));
	}

}