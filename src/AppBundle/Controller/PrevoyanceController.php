<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\ContratAgrrType;
use AppBundle\Entity\ContratPrevoyance;
use AppBundle\Form\ContratObsequeType;
use AppBundle\Entity\ContratPrevObs;


class PrevoyanceController extends Controller
{

    /**
     * @Route("/contact/{idContact}/contrats", name="list_contrats")
     * @Security("has_role('ROLE_USER')")
     */
    public function listContratsAction($idContact)
    {

    	$contact = $this->getDoctrine()
    		->getRepository('AppBundle:Contact')
    		->find($idContact);

    	// AGRR
    	$agrrs = $this->getDoctrine()
    		->getRepository('AppBundle:ContratPrevoyance')
    		->findBy(array('contact'=>$contact));

    	if($contact->getMembreConjoint()){
	    	$agrrsConjoint = $this->getDoctrine()
	    		->getRepository('AppBundle:ContratPrevoyance')
	    		->findBy(array('contact'=>$contact->getMembreConjoint()));

	    	$agrrs = array_merge($agrrs,$agrrsConjoint);
    	}

    	$agrr = new ContratPrevoyance();
	    $agrrForm = $this->createForm(ContratAgrrType::class, $agrr,array(
	        'action'=> $this->generateUrl('edit_agrr').'?idContact='.$contact->getId(),
	    ));

    	// Obseque
    	$obseques = $this->getDoctrine()
    		->getRepository('AppBundle:ContratPrevObs')
    		->findAll();

	    if($contact->getMembreConjoint()){
	    	$obsequesConjoint = $this->getDoctrine()
	    		->getRepository('AppBundle:ContratPrevObs')
	    		->findBy(array('contact'=>$contact->getMembreConjoint()));

	    	$obseques = array_merge($obseques,$obsequesConjoint);
    	}

    	$obseque = new ContratPrevObs();
	    $obsequeForm = $this->createForm(ContratObsequeType::class, $obseque,array(
	        'action'=> $this->generateUrl('edit_obseque').'?idContact='.$contact->getId(),
	    ));

    	return $this->render('operateur/contacts/prevoyances.html.twig',
    		[
    			'contact' => $contact,
    			'agrrs' => $agrrs,
    			'obseques' => $obseques,
    			'agrrForm' => $agrrForm->createView(),
    			'obsequeForm' => $obsequeForm->createView(),
    		]);
    }

    /**
     * @Route("/agrr/show-edit", name="show_edit_agrr")
     * @Security("has_role('ROLE_USER')")
     */
    public function showEditAgrrAction(Request $request)
    {

    	$agrr = $this->getDoctrine()
    		->getRepository('AppBundle:ContratPrevoyance')
    		->find($request->request->get('idAgrr'));

	    $agrrForm = $this->createForm(ContratAgrrType::class, $agrr,array(
	        'action'=> $this->generateUrl('edit_agrr').'?idAgrr='.$agrr->getId(),
	    ));

    	return new Response($this->render('modals/editer-agrr.html.twig',
    		[
    			'agrr' => $agrr,
    			'agrrForm' => $agrrForm->createView(),
    		])->getContent());
    }


	/**
	* @Route("/agrr/edit", name="edit_agrr")
	* @Security("has_role('ROLE_USER')")
	*/
	public function editAgrrAction(Request $request)
	{
	  		
	  	if($request->query->get('idAgrr')){
		    $agrr = $this->getDoctrine()
		      ->getRepository('AppBundle:ContratPrevoyance')
		      ->find($request->query->get('idAgrr'));
	    	
	    	$contact = $agrr->getContact();
	  	}else{
	  		$agrr = new ContratPrevoyance();
	  	
	  		$contact = $this->getDoctrine()
		      ->getRepository('AppBundle:Contact')
		      ->find($request->query->get('idContact'));
	  		
	  		$agrr->setContact($contact);
	  	}


	    $agrrForm = $this->createForm(ContratAgrrType::class, $agrr);
	    $agrrForm->handleRequest($request);

	    if ($agrrForm->isSubmitted() && $agrrForm->isValid()) {

	    	if ($agrr->getCible()=='CONJOINT') {
	    		$error = false;
	    		if ($agrr->getContact()->getMembreConjoint()) {
	    			$mConjoint = $agrr->getContact()->getMembreConjoint();
	    			$agrr->setContact($mConjoint);
	    			$agrr->setCible('CONTACT');
	    		}else{
					$error = true;
	    		}
	    	}

	    	if(!$error){
				$this->get('session')->getFlashBag()->add('danger', 'Impossible de lier le contrat au conjoint : aucun conjoint n\'a été défini');
	    	}else{	
				$em = $this->get('doctrine.orm.entity_manager');
				$em->persist($agrr);
				$em->flush();
	    	}

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
	    }
		if ($agrrForm->isSubmitted() && !$agrrForm->isValid()) {
			$this->get('session')->getFlashBag()->add('danger', 'Erreur lors de la validation du formulaire');
		}

	    return $this->redirectToRoute('list_contrats',array('idContact'=>$contact->getId()));
	}

	/**
	* @Route("/agrr/delete/{idAgrr}", name="delete_agrr")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteAgrrAction($idAgrr)
	{

	    $agrr = $this->getDoctrine()
	      ->getRepository('AppBundle:ContratPrevoyance')
	      ->find($idAgrr);
		
		$contact = $agrr->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($agrr);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_contrats',array('idContact'=>$contact->getId()));
	}

   	/**
     * @Route("/obseque/show-edit", name="show_edit_obseque")
     * @Security("has_role('ROLE_USER')")
     */
    public function showEditObsequeAction(Request $request)
    {

    	$obseque = $this->getDoctrine()
    		->getRepository('AppBundle:ContratPrevObs')
    		->find($request->request->get('idObseque'));

	    $obsequeForm = $this->createForm(ContratObsequeType::class, $obseque,array(
	        'action'=> $this->generateUrl('edit_obseque').'?idObseque='.$obseque->getId(),
	    ));

    	return new Response($this->render('modals/editer-obseque.html.twig',
    		[
    			'obseque' => $obseque,
    			'obsequeForm' => $obsequeForm->createView(),
    		])->getContent());
    }


	/**
	 * @Route("/obseque/edit", name="edit_obseque")
	 * @Security("has_role('ROLE_USER')")
	 */
	public function editObsequeAction(Request $request)
	{
  		
	  	if($request->query->get('idObseque')){
		    $obseque = $this->getDoctrine()
		      ->getRepository('AppBundle:ContratPrevObs')
		      ->find($request->query->get('idObseque'));
	    	
	    	$contact = $obseque->getContact();
	  	}else{
	  		$obseque = new ContratPrevObs();
	  	
	  		$contact = $this->getDoctrine()
		      ->getRepository('AppBundle:Contact')
		      ->find($request->query->get('idContact'));
	  		
	  		$obseque->setContact($contact);
	  	}


	    $obsequeForm = $this->createForm(ContratObsequeType::class, $obseque);
	    $obsequeForm->handleRequest($request);

	    if ($obsequeForm->isSubmitted() && $obsequeForm->isValid()) {

	    	if ($obseque->getCible()=='CONJOINT') {
	    		if ($obseque->getContact()->getMembreConjoint()) {
	    			$mConjoint = $obseque->getContact()->getMembreConjoint();
	    			$obseque->setContact($mConjoint);
	    			$obseque->setCible('CONTACT');
	    		}else{
	    			// throw exception
	    		}
	    	}


			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($obseque);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
	    }

	    return $this->redirectToRoute('list_contrats',array('idContact'=>$contact->getId()));
  	}

	/**
	* @Route("/obseque/delete/{idObseque}", name="delete_obseque")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteObsequeAction($idObseque)
	{

	    $obseque = $this->getDoctrine()
	      ->getRepository('AppBundle:ContratPrevObs')
	      ->find($idObseque);
		
		$contact = $obseque->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($obseque);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_contrats',array('idContact'=>$contact->getId()));
	}


}