<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\DocumentType;
use AppBundle\Entity\Document;
use AppBundle\Form\DossierType;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Suivi;
use AppBundle\Form\SuiviDefaultType;


class DocumentController extends Controller
{

	/**
	* @Route("/contact/{idContact}/documents", name="list_documents")
	* @Security("has_role('ROLE_USER')")
	*/
	public function listDocumentsAction($idContact)
	{
	  	$contact = $this->getDoctrine()
	  					->getRepository('AppBundle:Contact')
	  					->find($idContact);

	  	$documents = $this->getDoctrine()
	  					->getRepository('AppBundle:Document')
	  					->findBy(array('contact'=>$contact,'dossier'=>null));

	  	$dossiers = $this->getDoctrine()
	  					->getRepository('AppBundle:Dossier')
	  					->findBy(array('contact'=>$contact));

	  	$newDocumentForm = $this->createForm(DocumentType::class, new Document() ,array(
				'action'=> $this->generateUrl('save_document').'?idContact='.$contact->getId(),
			));


	  	$newDossierForm = $this->createForm(DossierType::class, new Dossier() ,array(
				'action'=> $this->generateUrl('save_dossier').'?idContact='.$contact->getId(),
			));

	    return $this->render('operateur/contacts/documents.html.twig',
	    	[
	    		'contact' => $contact,
	    		'documents' => $documents,
	    		'dossiers' => $dossiers,
	    		'newDocumentForm' => $newDocumentForm->createView(),
	    		'newDossierForm' => $newDossierForm->createView(),
	    	]);
	}

	/**
	* @Route("/contact/{idContact}/dossier/{idDossier}", name="view_dossier")
	* @Security("has_role('ROLE_USER')")
	*/
	public function viewDossierAction($idContact,$idDossier)
	{
	  	$contact = $this->getDoctrine()
	  					->getRepository('AppBundle:Contact')
	  					->find($idContact);

	  	$dossier = $this->getDoctrine()
	  					->getRepository('AppBundle:Dossier')
	  					->find($idDossier);

	  	$documents = $this->getDoctrine()
	  					->getRepository('AppBundle:Document')
	  					->findBy(array('contact'=>$contact,'dossier'=>$dossier));

	  	$suivis = $this->getDoctrine()
	  					->getRepository('AppBundle:Suivi')
	  					->findBy(array('dossier'=>$dossier,'isOk'=>false));

	  	$suivi = new Suivi();
      	$suiviForm = $this->createForm(SuiviDefaultType::class, $suivi,array('action'=>$this->generateUrl('save_suivi_dossier').'?idDossier='.$dossier->getId()));

	  	$dossiers = array();

	  	$newDocumentForm = $this->createForm(DocumentType::class, new Document() ,array(
				'action'=> $this->generateUrl('save_document').'?idContact='.$contact->getId(),
			));


	  	$newDossierForm = $this->createForm(DossierType::class, new Dossier() ,array(
				'action'=> $this->generateUrl('save_dossier').'?idContact='.$contact->getId(),
			));

		$lstAllSuivis = $this->getDoctrine()
		    ->getRepository('AppBundle:Suivi')
		    ->findBy(array('dossier'=>$dossier),array('dateCreation'=>'DESC'));

	    return $this->render('operateur/contacts/dossier.html.twig',
	    	[
	    		'contact' => $contact,
	    		'documents' => $documents,
	    		'dossiers' => $dossiers,
	    		'dossier' => $dossier,
	    		'lstSuivis' => $suivis,
	    		'lstAllSuivis' => $lstAllSuivis,
	    		'suiviForm' => $suiviForm->createView(),
	    		'newDocumentForm' => $newDocumentForm->createView(),
	    		'newDossierForm' => $newDossierForm->createView(),
	    	]);
	}

	/**
	* @Route("/document/save", name="save_document")
	* @Security("has_role('ROLE_USER')")
	*/
	public function saveDocumentAction(Request $request)
	{

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idDocument')){
			$document = $this->getDoctrine()
				->getRepository('AppBundle:Document')
				->find($request->query->get('idDocument'));
		}else{
			$document = new Document();
			$document->setOperateur($this->getUser());
			$document->setContact($contact);
		}

		$documentForm = $this->createForm(DocumentType::class, $document);
		$documentForm->handleRequest($request);

		if ($documentForm->isSubmitted() && $documentForm->isValid()) {
			
			$document->upload();

			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($document);
			$em->flush();

		}

		return $this->redirectToRoute('list_documents',array('idContact'=>$contact->getId()));
	}

	/**
	* @Route("/dossier/save", name="save_dossier")
	* @Security("has_role('ROLE_USER')")
	*/
	public function saveDossierAction(Request $request)
	{

		$datetime = new \DateTime();

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idDossier')){
			$dossier = $this->getDoctrine()
				->getRepository('AppBundle:Dossier')
				->find($request->query->get('idDossier'));
		}else{
			$dossier = new Dossier();
			$dossier->setDateCreation($datetime);
			$dossier->setOperateur($this->getUser());
			$dossier->setContact($contact);
		}

		$dossierForm = $this->createForm(DossierType::class, $dossier);
		$dossierForm->handleRequest($request);

		if ($dossierForm->isSubmitted() && $dossierForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($dossier);
			$em->flush();
		}

		return $this->redirectToRoute('list_documents',array('idContact'=>$contact->getId()));
	}


	/**
	* @Route("/document/download/{idDocument}", name="download_document")
	* @Security("has_role('ROLE_USER')")
	*/
	public function downloadDocumentAction($idDocument)
	{

	    $document = $this->getDoctrine()
	      ->getRepository('AppBundle:Document')
	      ->find($idDocument);
		
		$response = new Response();
		$response->setContent(file_get_contents($document->getAbsolutePath()));
		$response->headers->set(
		   'Content-Type',
		   'application/force-download'
		);
		$response->headers->set('Content-disposition', 'filename='.$document->getLabel().'.'.$document->getType());

		return $response;
	}

	    /**
     * @Route("/document/show-edit", name="show_edit_document")
     * @Security("has_role('ROLE_USER')")
     */
    public function showEditDocumentAction(Request $request)
    {

    	$document = $this->getDoctrine()
    		->getRepository('AppBundle:Document')
    		->find($request->request->get('idDocument'));

	    $documentForm = $this->createForm(DocumentType::class, $document,array(
	        'action'=> $this->generateUrl('save_document').'?idDocument='.$document->getId().'&idContact='.$document->getContact()->getId(),
	    ));

    	return new Response($this->render('modals/editer-document.html.twig',
    		[
    			'document' => $document,
    			'documentForm' => $documentForm->createView(),
    		])->getContent());
    }

	/**
	* @Route("/document/delete/{idDocument}", name="delete_document")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteDocumentAction($idDocument)
	{

	    $document = $this->getDoctrine()
	      ->getRepository('AppBundle:Document')
	      ->find($idDocument);
		
		$contact = $document->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($document);
		$em->flush();

	    return $this->redirectToRoute('list_documents',array('idContact'=>$contact->getId()));
	}

	/**
	* @Route("/dossier/delete/{idDossier}", name="delete_dossier")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteDossierAction($idDossier)
	{

	    $dossier = $this->getDoctrine()
	      ->getRepository('AppBundle:Dossier')
	      ->find($idDossier);
		
		$contact = $dossier->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($dossier);
		$em->flush();

	    return $this->redirectToRoute('list_documents',array('idContact'=>$contact->getId()));
	}

}