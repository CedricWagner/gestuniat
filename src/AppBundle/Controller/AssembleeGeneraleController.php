<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\AssembleeGeneraleType;
use AppBundle\Entity\AssembleeGenerale;


class AssembleeGeneraleController extends Controller
{

	/**
	* @Route("/section/{idSection}/assemblees-generales", name="list_assembleeGenerales")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function listAssembleeGeneralesAction($idSection)
	{
	  	$section = $this->getDoctrine()
	  					->getRepository('AppBundle:Section')
	  					->find($idSection);

	  	$assembleeGenerales = $this->getDoctrine()
	  					->getRepository('AppBundle:AssembleeGenerale')
	  					->findBy(array('section'=>$section));

	  	$assembleeGeneraleForms = array();

	  	foreach ($assembleeGenerales as $assembleeGenerale) {
	  		$assembleeGeneraleForm = $this->createForm(AssembleeGeneraleType::class, $assembleeGenerale,array(
	  				'action'=> $this->generateUrl('save_assembleeGenerale').'?idSection='.$section->getId().'&idAssembleeGenerale='.$assembleeGenerale->getId(),
	  			));
	  		$assembleeGeneraleForms[$assembleeGenerale->getId()] = $assembleeGeneraleForm->createView();
	  	}

	  	$newAssembleeGeneraleForm = $this->createForm(AssembleeGeneraleType::class, new AssembleeGenerale() ,array(
				'action'=> $this->generateUrl('save_assembleeGenerale').'?idSection='.$section->getId(),
			));

	    return $this->render('operateur/sections/assemblee-generales.html.twig',
	    	[
	    		'section' => $section,
	    		'assembleeGenerales' => $assembleeGenerales,
	    		'assembleeGeneraleForms' => $assembleeGeneraleForms,
	    		'newAssembleeGeneraleForm' => $newAssembleeGeneraleForm->createView(),
	    	]);
	}

	/**
	* @Route("/assembleeGenerale/save", name="save_assembleeGenerale")
	* @Security("has_role('ROLE_USER')")
	*/
	public function saveAssembleeGeneraleAction(Request $request)
	{

		$datetime = new \DateTime();

		$section = $this->getDoctrine()
				->getRepository('AppBundle:Section')
				->find($request->query->get('idSection'));

		if($request->query->get('idAssembleeGenerale')){
			$assembleeGenerale = $this->getDoctrine()
				->getRepository('AppBundle:AssembleeGenerale')
				->find($request->query->get('idAssembleeGenerale'));
		}else{
			$assembleeGenerale = new AssembleeGenerale();
			$assembleeGenerale->setSection($section);
		}

		$assembleeGeneraleForm = $this->createForm(AssembleeGeneraleType::class, $assembleeGenerale);
		$assembleeGeneraleForm->handleRequest($request);

		if ($assembleeGeneraleForm->isSubmitted() && $assembleeGeneraleForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($assembleeGenerale);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($assembleeGeneraleForm->isSubmitted() && !$assembleeGeneraleForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($assembleeGeneraleForm);
		}

		return $this->redirectToRoute('list_assembleeGenerales',array('idSection'=>$section->getId()));
	}


	/**
	* @Route("/assembleeGenerale/delete/{idAssembleeGenerale}", name="delete_assembleeGenerale")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteAssembleeGeneraleAction($idAssembleeGenerale)
	{

	    $assembleeGenerale = $this->getDoctrine()
	      ->getRepository('AppBundle:AssembleeGenerale')
	      ->find($idAssembleeGenerale);
		
		$section = $assembleeGenerale->getSection();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($assembleeGenerale);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_assembleeGenerales',array('idSection'=>$section->getId()));
	}

}