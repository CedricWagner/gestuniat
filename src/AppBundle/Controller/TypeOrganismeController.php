<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\TypeOrganismeType;
use AppBundle\Entity\TypeOrganisme;


class TypeOrganismeController extends Controller
{

	/**
	* @Route("/admin/types-organisme", name="list_typeOrganismes")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function listTypeOrganismesAction()
	{

	  	$typeOrganismes = $this->getDoctrine()
	  					->getRepository('AppBundle:TypeOrganisme')
	  					->findAll();

	  	$typeOrganismeForms = array();

	  	foreach ($typeOrganismes as $typeOrganisme) {
	  		$typeOrganismeForm = $this->createForm(TypeOrganismeType::class, $typeOrganisme,array(
	  				'action'=> $this->generateUrl('save_typeOrganisme').'?idTypeOrganisme='.$typeOrganisme->getId(),
	  			));
	  		$typeOrganismeForms[$typeOrganisme->getId()] = $typeOrganismeForm->createView();
	  	}

	  	$newTypeOrganismeForm = $this->createForm(TypeOrganismeType::class, new TypeOrganisme() ,array(
				'action'=> $this->generateUrl('save_typeOrganisme'),
			));

	    return $this->render('admin/types-organisme.html.twig',
	    	[
	    		'typeOrganismes' => $typeOrganismes,
	    		'typeOrganismeForms' => $typeOrganismeForms,
	    		'newTypeOrganismeForm' => $newTypeOrganismeForm->createView(),
	    	]);
	}

	/**
	* @Route("/typeOrganisme/save", name="save_typeOrganisme")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function saveTypeOrganismeAction(Request $request)
	{

		if($request->query->get('idTypeOrganisme')){
			$typeOrganisme = $this->getDoctrine()
				->getRepository('AppBundle:TypeOrganisme')
				->find($request->query->get('idTypeOrganisme'));
		}else{
			$typeOrganisme = new TypeOrganisme();
		}

		$typeOrganismeForm = $this->createForm(TypeOrganismeType::class, $typeOrganisme);
		$typeOrganismeForm->handleRequest($request);

		if ($typeOrganismeForm->isSubmitted() && $typeOrganismeForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($typeOrganisme);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($typeOrganismeForm->isSubmitted() && !$typeOrganismeForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($typeOrganismeForm);
		}

		return $this->redirectToRoute('list_typeOrganismes');
	}


	/**
	* @Route("/typeOrganisme/delete/{idTypeOrganisme}", name="delete_typeOrganisme")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function deleteTypeOrganismeAction($idTypeOrganisme)
	{

	    $typeOrganisme = $this->getDoctrine()
	      ->getRepository('AppBundle:TypeOrganisme')
	      ->find($idTypeOrganisme);

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($typeOrganisme);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_typeOrganismes');
	}

}