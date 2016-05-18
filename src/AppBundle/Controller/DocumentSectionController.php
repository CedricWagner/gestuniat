<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\DocumentSectionType;
use AppBundle\Entity\DocumentSection;


class DocumentSectionController extends Controller
{

	/**
	* @Route("/section/{idSection}/documents", name="list_documentSections")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function listDocumentsAction($idSection)
	{
	  	$section = $this->getDoctrine()
	  					->getRepository('AppBundle:Section')
	  					->find($idSection);

	  	$documents = $this->getDoctrine()
	  					->getRepository('AppBundle:DocumentSection')
	  					->findBy(array('section'=>$section));

	  	$newDocumentSectionForm = $this->createForm(DocumentSectionType::class, new DocumentSection() ,array(
				'action'=> $this->generateUrl('save_documentSection').'?idSection='.$section->getId(),
			));


	    return $this->render('operateur/sections/documents.html.twig',
	    	[
	    		'section' => $section,
	    		'documents' => $documents,
	    		'newDocumentSectionForm' => $newDocumentSectionForm->createView(),
	    	]);
	}

	/**
	* @Route("/section/document/save", name="save_documentSection")
	* @Security("has_role('ROLE_USER')")
	*/
	public function saveDocumentAction(Request $request)
	{

		$section = $this->getDoctrine()
				->getRepository('AppBundle:Section')
				->find($request->query->get('idSection'));

		if($request->query->get('idDocumentSection')){
			$document = $this->getDoctrine()
				->getRepository('AppBundle:DocumentSection')
				->find($request->query->get('idDocumentSection'));
		}else{
			$document = new DocumentSection();
			$document->setOperateur($this->getUser());
			$document->setSection($section);
		}

		$documentSectionForm = $this->createForm(DocumentSectionType::class, $document);
		$documentSectionForm->handleRequest($request);

		if ($documentSectionForm->isSubmitted() && $documentSectionForm->isValid()) {
			
			$document->upload();

			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($document);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

		}

		return $this->redirectToRoute('list_documentSections',array('idSection'=>$section->getId()));
	}

	/**
	* @Route("/section/document/download/{idDocumentSection}", name="download_documentSection")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function downloadDocumentAction($idDocumentSection)
	{

	    $document = $this->getDoctrine()
	      ->getRepository('AppBundle:DocumentSection')
	      ->find($idDocumentSection);
		
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
     * @Route("/section/document/show-edit", name="show_edit_documentSection")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function showEditDocumentAction(Request $request)
    {

    	$document = $this->getDoctrine()
    		->getRepository('AppBundle:DocumentSection')
    		->find($request->request->get('idDocumentSection'));

	    $documentSectionForm = $this->createForm(DocumentSectionType::class, $document,array(
	        'action'=> $this->generateUrl('save_documentSection').'?idDocumentSection='.$document->getId().'&idSection='.$document->getSection()->getId(),
	    ));

    	return new Response($this->render('modals/editer-document-section.html.twig',
    		[
    			'documentSection' => $document,
    			'documentSectionForm' => $documentSectionForm->createView(),
    		])->getContent());
    }

	/**
	* @Route("/section/document/delete/{idDocumentSection}", name="delete_documentSection")
	* @Security("has_role('ROLE_USER')")
	*/
	public function deleteDocumentAction($idDocumentSection)
	{

	    $document = $this->getDoctrine()
	      ->getRepository('AppBundle:DocumentSection')
	      ->find($idDocumentSection);
		
		$section = $document->getSection();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($document);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_documentSections',array('idSection'=>$section->getId()));
	}


}