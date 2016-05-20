<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\PermanenceType;
use AppBundle\Entity\Permanence;


class PermanenceController extends Controller
{

	/**
	* @Route("/section/{idSection}/permanences", name="list_permanences")
	* @Security("has_role('ROLE_USER')")
	*/
	public function listPermanencesAction($idSection)
	{
	  	$section = $this->getDoctrine()
	  					->getRepository('AppBundle:Section')
	  					->find($idSection);

	  	$permanences = $this->getDoctrine()
	  					->getRepository('AppBundle:Permanence')
	  					->findBy(array('section'=>$section));

	  	$permanenceForms = array();

	  	foreach ($permanences as $permanence) {
	  		$permanenceForm = $this->createForm(PermanenceType::class, $permanence,array(
	  				'action'=> $this->generateUrl('save_permanence').'?idSection='.$section->getId().'&idPermanence='.$permanence->getId(),
	  			));
	  		$permanenceForms[$permanence->getId()] = $permanenceForm->createView();
	  	}

	  	$newPermanenceForm = $this->createForm(PermanenceType::class, new Permanence() ,array(
				'action'=> $this->generateUrl('save_permanence').'?idSection='.$section->getId(),
			));

	    return $this->render('operateur/sections/permanences.html.twig',
	    	[
	    		'section' => $section,
	    		'permanences' => $permanences,
	    		'permanenceForms' => $permanenceForms,
	    		'newPermanenceForm' => $newPermanenceForm->createView(),
	    	]);
	}

	/**
	* @Route("/permanence/save", name="save_permanence")
	* @Security("has_role('ROLE_USER')")
	*/
	public function savePermanenceAction(Request $request)
	{

		$datetime = new \DateTime();

		$section = $this->getDoctrine()
				->getRepository('AppBundle:Section')
				->find($request->query->get('idSection'));

		if($request->query->get('idPermanence')){
			$permanence = $this->getDoctrine()
				->getRepository('AppBundle:Permanence')
				->find($request->query->get('idPermanence'));
		}else{
			$permanence = new Permanence();
			$permanence->setSection($section);
		}

		$permanenceForm = $this->createForm(PermanenceType::class, $permanence);
		$permanenceForm->handleRequest($request);

		$permanence->setDateMAJ($datetime);

		if ($permanenceForm->isSubmitted() && $permanenceForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($permanence);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($permanenceForm->isSubmitted() && !$permanenceForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($permanenceForm);
		}

		return $this->redirectToRoute('list_permanences',array('idSection'=>$section->getId()));
	}


	/**
	* @Route("/permanence/delete/{idPermanence}", name="delete_permanence")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deletePermanenceAction($idPermanence)
	{

	    $permanence = $this->getDoctrine()
	      ->getRepository('AppBundle:Permanence')
	      ->find($idPermanence);
		
		$section = $permanence->getSection();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($permanence);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_permanences',array('idSection'=>$section->getId()));
	}

}