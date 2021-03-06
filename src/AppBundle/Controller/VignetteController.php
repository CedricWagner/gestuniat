<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\VignetteType;
use AppBundle\Entity\Vignette;


class VignetteController extends Controller
{

	/**
	* @Route("/contact/{idContact}/vignettes", name="list_vignettes")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function listVignettesAction($idContact)
	{
		$this->get('app.security')->checkAccess('VIGNETTE_READ');

	  	$contact = $this->getDoctrine()
	  					->getRepository('AppBundle:Contact')
	  					->find($idContact);

	  	$vignettes = $this->getDoctrine()
	  					->getRepository('AppBundle:Vignette')
	  					->findBy(array('contact'=>$contact),array('dateDemande'=>'ASC'));

	  	$vignetteForms = array();

	  	foreach ($vignettes as $vignette) {
	  		$vignetteForm = $this->createForm(VignetteType::class, $vignette,array(
	  				'action'=> $this->generateUrl('save_vignette').'?idContact='.$contact->getId().'&idVignette='.$vignette->getId(),
	  			));
	  		$vignetteForms[$vignette->getId()] = $vignetteForm->createView();
	  	}

	  	$newVignette = new Vignette();
	  	$newVignette->setContact($contact);
	  	$newVignetteForm = $this->createForm(VignetteType::class, $newVignette ,array(
				'action'=> $this->generateUrl('save_vignette').'?idContact='.$contact->getId(),
			));

	    return $this->render('operateur/contacts/vignettes.html.twig',
	    	[
	    		'contact' => $contact,
	    		'vignettes' => $vignettes,
	    		'vignetteForms' => $vignetteForms,
	    		'newVignetteForm' => $newVignetteForm->createView(),
	    	]);
	}

	/**
	* @Route("/vignette/save", name="save_vignette")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function saveVignetteAction(Request $request)
	{
		$this->get('app.security')->checkAccess('VIGNETTE_WRITE');

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idVignette')){
			$vignette = $this->getDoctrine()
				->getRepository('AppBundle:Vignette')
				->find($request->query->get('idVignette'));
		}else{
			$vignette = new Vignette();
			$vignette->setContact($contact);
			$vignette->setOperateur($this->getUser());
		}

		$vignetteForm = $this->createForm(VignetteType::class, $vignette);
		$vignetteForm->handleRequest($request);

		if ($vignetteForm->isSubmitted() && $vignetteForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($vignette);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($vignetteForm->isSubmitted() && !$vignetteForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($vignetteForm);
		}

    	return $this->redirect($request->headers->get('referer'));
	}


	/**
	* @Route("/vignette/delete/{idVignette}", name="delete_vignette")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function deleteVignetteAction($idVignette)
	{
		$this->get('app.security')->checkAccess('VIGNETTE_DELETE');

	    $vignette = $this->getDoctrine()
	      ->getRepository('AppBundle:Vignette')
	      ->find($idVignette);
		
		$contact = $vignette->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($vignette);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_vignettes',array('idContact'=>$contact->getId()));
	}

}