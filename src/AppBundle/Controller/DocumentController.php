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
			$document->setContact($contact);
		}

		$documentForm = $this->createForm(DocumentType::class, $document);
		$documentForm->handleRequest($request);

		if ($documentForm->isSubmitted() && $documentForm->isValid()) {
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
	public function savedossierAction(Request $request)
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
			$document->setContact($contact);
		}

		$documentForm = $this->createForm(DocumentType::class, $document);
		$documentForm->handleRequest($request);

		if ($documentForm->isSubmitted() && $documentForm->isValid()) {
			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($document);
			$em->flush();
		}

		return $this->redirectToRoute('list_documents',array('idContact'=>$contact->getId()));
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

}