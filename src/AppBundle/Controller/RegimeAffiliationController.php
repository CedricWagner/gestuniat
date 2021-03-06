<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\RegimeAffiliationType;
use AppBundle\Entity\RegimeAffiliation;


class RegimeAffiliationController extends Controller
{

	/**
	* @Route("/admin/regimes-affiliation", name="list_regimeAffiliations")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listRegimeAffiliationsAction()
	{

	  	$regimeAffiliations = $this->getDoctrine()
	  					->getRepository('AppBundle:RegimeAffiliation')
	  					->findAll();

	  	$regimeAffiliationForms = array();

	  	foreach ($regimeAffiliations as $regimeAffiliation) {
	  		$regimeAffiliationForm = $this->createForm(RegimeAffiliationType::class, $regimeAffiliation,array(
	  				'action'=> $this->generateUrl('save_regimeAffiliation').'?idRegimeAffiliation='.$regimeAffiliation->getId(),
	  			));
	  		$regimeAffiliationForms[$regimeAffiliation->getId()] = $regimeAffiliationForm->createView();
	  	}

	  	$newRegimeAffiliationForm = $this->createForm(RegimeAffiliationType::class, new RegimeAffiliation() ,array(
				'action'=> $this->generateUrl('save_regimeAffiliation'),
			));

	    return $this->render('admin/regimes-affiliation.html.twig',
	    	[
	    		'regimeAffiliations' => $regimeAffiliations,
	    		'regimeAffiliationForms' => $regimeAffiliationForms,
	    		'newRegimeAffiliationForm' => $newRegimeAffiliationForm->createView(),
	    	]);
	}

	/**
	* @Route("/regimeAffiliation/save", name="save_regimeAffiliation")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function saveRegimeAffiliationAction(Request $request)
	{

		if($request->query->get('idRegimeAffiliation')){
			$regimeAffiliation = $this->getDoctrine()
				->getRepository('AppBundle:RegimeAffiliation')
				->find($request->query->get('idRegimeAffiliation'));
		}else{
			$regimeAffiliation = new RegimeAffiliation();
		}

		$regimeAffiliationForm = $this->createForm(RegimeAffiliationType::class, $regimeAffiliation);
		$regimeAffiliationForm->handleRequest($request);

		if ($regimeAffiliationForm->isSubmitted() && $regimeAffiliationForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($regimeAffiliation);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($regimeAffiliationForm->isSubmitted() && !$regimeAffiliationForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($regimeAffiliationForm);
		}

		return $this->redirectToRoute('list_regimeAffiliations');
	}


	/**
	* @Route("/regimeAffiliation/delete/{idRegimeAffiliation}", name="delete_regimeAffiliation")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function deleteRegimeAffiliationAction($idRegimeAffiliation)
	{

	    $regimeAffiliation = $this->getDoctrine()
	      ->getRepository('AppBundle:RegimeAffiliation')
	      ->find($idRegimeAffiliation);

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($regimeAffiliation);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_regimeAffiliations');
	}

}