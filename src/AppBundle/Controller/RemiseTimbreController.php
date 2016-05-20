<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\RemiseTimbreType;
use AppBundle\Entity\RemiseTimbre;


class RemiseTimbreController extends Controller
{

	/**
	* @Route("/section/{idSection}/remiseTimbres", name="list_remiseTimbres")
	* @Security("has_role('ROLE_USER')")
	*/
	public function listRemiseTimbresAction($idSection)
	{
	  	$section = $this->getDoctrine()
	  					->getRepository('AppBundle:Section')
	  					->find($idSection);

	  	$remiseTimbres = $this->getDoctrine()
	  					->getRepository('AppBundle:RemiseTimbre')
	  					->findBy(array('section'=>$section),array('annee'=>'ASC'));

	  	$remiseTimbreForms = array();

	  	foreach ($remiseTimbres as $remiseTimbre) {
	  		$remiseTimbreForm = $this->createForm(RemiseTimbreType::class, $remiseTimbre,array(
	  				'action'=> $this->generateUrl('save_remiseTimbre').'?idSection='.$section->getId().'&idRemiseTimbre='.$remiseTimbre->getId(),
	  			));
	  		$remiseTimbreForms[$remiseTimbre->getId()] = $remiseTimbreForm->createView();
	  	}

	  	$newRemiseTimbreForm = $this->createForm(RemiseTimbreType::class, new RemiseTimbre() ,array(
				'action'=> $this->generateUrl('save_remiseTimbre').'?idSection='.$section->getId(),
			));

	    return $this->render('operateur/sections/remises-timbres.html.twig',
	    	[
	    		'section' => $section,
	    		'remiseTimbres' => $remiseTimbres,
	    		'remiseTimbreForms' => $remiseTimbreForms,
	    		'newRemiseTimbreForm' => $newRemiseTimbreForm->createView(),
	    	]);
	}

	/**
	* @Route("/remiseTimbre/save", name="save_remiseTimbre")
	* @Security("has_role('ROLE_USER')")
	*/
	public function saveRemiseTimbreAction(Request $request)
	{

		$section = $this->getDoctrine()
				->getRepository('AppBundle:Section')
				->find($request->query->get('idSection'));

		if($request->query->get('idRemiseTimbre')){
			$remiseTimbre = $this->getDoctrine()
				->getRepository('AppBundle:RemiseTimbre')
				->find($request->query->get('idRemiseTimbre'));
		}else{
			$remiseTimbre = new RemiseTimbre();
			$remiseTimbre->setSection($section);
		}

		$remiseTimbreForm = $this->createForm(RemiseTimbreType::class, $remiseTimbre);
		$remiseTimbreForm->handleRequest($request);

		if ($remiseTimbreForm->isSubmitted() && $remiseTimbreForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($remiseTimbre);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($remiseTimbreForm->isSubmitted() && !$remiseTimbreForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($remiseTimbreForm);
		}

		return $this->redirectToRoute('list_remiseTimbres',array('idSection'=>$section->getId()));
	}


	/**
	* @Route("/remiseTimbre/delete/{idRemiseTimbre}", name="delete_remiseTimbre")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteRemiseTimbreAction($idRemiseTimbre)
	{

	    $remiseTimbre = $this->getDoctrine()
	      ->getRepository('AppBundle:RemiseTimbre')
	      ->find($idRemiseTimbre);
		
		$section = $remiseTimbre->getSection();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($remiseTimbre);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_remiseTimbres',array('idSection'=>$section->getId()));
	}

}