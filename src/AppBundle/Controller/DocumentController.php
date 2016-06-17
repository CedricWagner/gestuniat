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
use AppBundle\Form\DossierFullType;
use AppBundle\Entity\Dossier;
use AppBundle\Form\VignetteType;
use AppBundle\Entity\Vignette;
use AppBundle\Entity\Suivi;
use AppBundle\Form\SuiviDefaultType;


class DocumentController extends Controller
{

	/**
	* @Route("/contact/{idContact}/documents", name="list_documents")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function listDocumentsAction($idContact)
	{

		$this->get('app.security')->checkAccess('DOSSIER_READ');

	  	$contact = $this->getDoctrine()
	  					->getRepository('AppBundle:Contact')
	  					->find($idContact);

	  	$documents = $this->getDoctrine()
	  					->getRepository('AppBundle:Document')
	  					->findBy(array('contact'=>$contact,'dossier'=>null));

	  	$dossiers = $this->getDoctrine()
	  					->getRepository('AppBundle:Dossier')
	  					->findBy(array('contact'=>$contact));

	  	$newDocument = new Document();
	  	$newDocument->setContact($contact);

	  	$newDocumentForm = $this->createForm(DocumentType::class, $newDocument ,array(
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
     * @Route("/dossier/liste/{idFilter}/{page}/{nb}", name="list_dossiers", defaults={"idFilter" = 0,"page" = 1,"nb" = 0,"orderby" = "nom","order" = "ASC"})
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function listDossiersAction($idFilter,$page,$nb,$orderby,$order)
    {

    	$this->get('app.security')->checkAccess('DOSSIER_READ');

        $currentFilter = null;

        if ($idFilter!=0) {
            $currentFilter = $this->getDoctrine()
              ->getRepository('AppBundle:FiltrePerso')
              ->find($idFilter);

            $filtreValeurs = $this->getDoctrine()
              ->getRepository('AppBundle:FiltreValeur')
              ->findBy(array('filtrePerso'=>$currentFilter));

            $currentFilter->setFiltreValeurs($filtreValeurs);
        }

        $session = $this->get('session');
        if($nb==0){
            if($session->get('pagination-nb')){
                $nb = $session->get('pagination-nb');
            }else{
                $nb=20;
            }
        }else{
            $session->set('pagination-nb', $nb);
        }

        $filtresPerso = $this->getDoctrine()
                          ->getRepository('AppBundle:FiltrePerso')
                          ->findBy(array('operateur'=>$this->getUser(),'contexte'=>'dossier'),array('label'=>'ASC'));

        $sections = $this->getDoctrine()
                              ->getRepository('AppBundle:Section')
                              ->findBy(array(),array('nom'=>'ASC'));

        if($currentFilter){
          $dossiers = $this->getDoctrine()
                      ->getRepository('AppBundle:Dossier')
                      ->findByFilter($filtreValeurs,$page,$nb);
        }else{
          $dossiers = $this->getDoctrine()
                      ->getRepository('AppBundle:Dossier')
                      ->findAllWithPagination($page,$nb);
        }

        return $this->render('operateur/dossiers/dossiers.html.twig', [
            'filtresPerso' => $filtresPerso,
            'currentFilter' => $currentFilter,
            'items' => $dossiers,
            'sections' => $sections,
            'pagination' => array('count'=>count($dossiers),'nb'=>$nb,'page'=>$page,'orderby'=>$orderby,'order'=>$order),
        ]);
    }


	/**
	* @Route("/contact/{idContact}/dossier/{idDossier}", name="view_dossier")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function viewDossierAction($idContact,$idDossier)
	{

		$this->get('app.security')->checkAccess('DOSSIER_READ');

	  	$contact = $this->getDoctrine()
	  					->getRepository('AppBundle:Contact')
	  					->find($idContact);

	  	$dossier = $this->getDoctrine()
	  					->getRepository('AppBundle:Dossier')
	  					->find($idDossier);

	  	$documents = $this->getDoctrine()
	  					->getRepository('AppBundle:Document')
	  					->findBy(array('contact'=>$contact,'dossier'=>$dossier));

	  	$vignettes = $this->getDoctrine()
	  					->getRepository('AppBundle:Vignette')
	  					->findBy(array('dossier'=>$dossier,));

	  	$suivis = $this->getDoctrine()
	  					->getRepository('AppBundle:Suivi')
	  					->findBy(array('dossier'=>$dossier,'isOk'=>false));

	  	$suivi = new Suivi();
      	$suiviForm = $this->createForm(SuiviDefaultType::class, $suivi,array('action'=>$this->generateUrl('save_suivi_dossier').'?idDossier='.$dossier->getId()));

	  	$dossiers = array();

	  	$newDocument = new Document();
	  	$newDocument->setContact($contact);

	  	$newDocumentForm = $this->createForm(DocumentType::class, $newDocument ,array(
				'action'=> $this->generateUrl('save_document').'?idContact='.$contact->getId(),
			));


	  	$newDossierForm = $this->createForm(DossierType::class, new Dossier() ,array(
				'action'=> $this->generateUrl('save_dossier').'?idContact='.$contact->getId(),
			));

	  	$dossierForm = $this->createForm(DossierFullType::class, $dossier ,array(
				'action'=> $this->generateUrl('save_dossier_full').'?idContact='.$contact->getId().'&idDossier='.$dossier->getId(),
			));

	  	$newVignette = new Vignette();
	  	$newVignette->setContact($contact);
	  	$newVignette->setDossier($dossier);
	  	$newVignetteForm = $this->createForm(VignetteType::class, $newVignette ,array(
				'action'=> $this->generateUrl('save_vignette').'?idContact='.$contact->getId().'&idDossier='.$dossier->getId(),
			));

	  	$vignetteForms = array();

	  	foreach ($vignettes as $vignette) {
	  		$vignetteForm = $this->createForm(VignetteType::class, $vignette,array(
	  				'action'=> $this->generateUrl('save_vignette').'?idContact='.$contact->getId().'&idVignette='.$vignette->getId(),
	  			));
	  		$vignetteForms[$vignette->getId()] = $vignetteForm->createView();
	  	}

		$lstAllSuivis = $this->getDoctrine()
		    ->getRepository('AppBundle:Suivi')
		    ->findBy(array('dossier'=>$dossier),array('dateCreation'=>'DESC'));

	    return $this->render('operateur/contacts/dossier.html.twig',
	    	[
	    		'contact' => $contact,
	    		'documents' => $documents,
	    		'dossiers' => $dossiers,
	    		'vignettes' => $vignettes,
	    		'dossier' => $dossier,
	    		'lstSuivis' => $suivis,
	    		'lstAllSuivis' => $lstAllSuivis,
	    		'suiviForm' => $suiviForm->createView(),
	    		'dossierForm' => $dossierForm->createView(),
	    		'newDocumentForm' => $newDocumentForm->createView(),
	    		'newDossierForm' => $newDossierForm->createView(),
	    		'newVignetteForm' => $newVignetteForm->createView(),
	    		'vignetteForms' => $vignetteForms,
	    	]);
	}

	/**
	* @Route("/document/save", name="save_document")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function saveDocumentAction(Request $request)
	{

		$this->get('app.security')->checkAccess('DOSSIER_WRITE');

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idDocument')){
			$document = $this->getDoctrine()
				->getRepository('AppBundle:Document')
				->find($request->query->get('idDocument'));
			$document->setOperateur($this->getUser());
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

			$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

		}
		if ($documentForm->isSubmitted() && !$documentForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($documentForm);
		}

		return $this->redirectToRoute('list_documents',array('idContact'=>$contact->getId()));
	}

	/**
	* @Route("/dossier/save", name="save_dossier")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function saveDossierAction(Request $request)
	{

		$this->get('app.security')->checkAccess('DOSSIER_WRITE');

		$datetime = new \DateTime();

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idDossier')){
			$dossier = $this->getDoctrine()
				->getRepository('AppBundle:Dossier')
				->find($request->query->get('idDossier'));
			$dossier->setOperateur($this->getUser());
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

          	$history = $this->get('app.history');
          	$history->init($this->getUser(),['id'=>$dossier->getId(),'name'=>'Dossier'],$request->query->get('idDossier')?'UPDATE':'INSERT')
                  	->log(true); 

          	$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($dossierForm->isSubmitted() && !$dossierForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($dossierForm);
		}

	    return $this->redirect($request->headers->get('referer'));
	}

	/**
	* @Route("/dossier/save/full", name="save_dossier_full")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function saveDossierFullAction(Request $request)
	{

		$this->get('app.security')->checkAccess('DOSSIER_WRITE');

		$datetime = new \DateTime();

		$contact = $this->getDoctrine()
				->getRepository('AppBundle:Contact')
				->find($request->query->get('idContact'));

		if($request->query->get('idDossier')){
			$dossier = $this->getDoctrine()
				->getRepository('AppBundle:Dossier')
				->find($request->query->get('idDossier'));
			$dossier->setOperateur($this->getUser());
			$lastDateFermeture = $dossier->getDateFermeture();
		}else{
			$dossier = new Dossier();
			$dossier->setDateCreation($datetime);
			$dossier->setOperateur($this->getUser());
			$dossier->setContact($contact);
		}

		$dossierForm = $this->createForm(DossierFullType::class, $dossier);
		$dossierForm->handleRequest($request);

		if ($dossierForm->isSubmitted() && $dossierForm->isValid()) {

			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($dossier);
			$em->flush();

          	$history = $this->get('app.history');
          	$history->init($this->getUser(),['id'=>$dossier->getId(),'name'=>'Dossier'],$request->query->get('idDossier')?'UPDATE':'INSERT')
                  	->log(true); 

			//just closed
			if($lastDateFermeture == null && $dossier->getDateFermeture() != null){
				$vignettes = $this->getDoctrine()
					->getRepository('AppBundle:Vignette')
					->findBy(array('dossier'=>$dossier,'datePaiement'=>null));

				if(sizeof($vignettes)){
					$dossier->setDateFermeture(null);
					$em->persist($dossier);
					$em->flush();

          			$this->get('session')->getFlashBag()->add('danger', 'impossible de fermer le dossier : il reste des vignettes impayées');
					
				}else{
					$suivi = new Suivi();
					$suivi
						->setOperateur($this->getUser())
						->setDateCreation($datetime)
						->setTexte('Cloture du dossier')
						->setIsOk(true)
						->setContact($contact)
						->setDossier($dossier)
						;
					$em->persist($suivi);
					$em->flush();
				}
			}

          	$this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
		}
		if ($dossierForm->isSubmitted() && !$dossierForm->isValid()) {
			$this->get('app.tools')->handleFormErrors($dossierForm);
		}

	    return $this->redirect($request->headers->get('referer'));
	}


	/**
	* @Route("/document/download/{idDocument}", name="download_document")
	* @Security("has_role('ROLE_SPECTATOR')")
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
     * @Security("has_role('ROLE_SPECTATOR')")
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
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function deleteDocumentAction($idDocument)
	{

		$this->get('app.security')->checkAccess('DOSSIER_DELETE');

	    $document = $this->getDoctrine()
	      ->getRepository('AppBundle:Document')
	      ->find($idDocument);
		
		$contact = $document->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($document);
		$em->flush();

      	$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_documents',array('idContact'=>$contact->getId()));
	}

	/**
	* @Route("/dossier/delete/{idDossier}", name="delete_dossier")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function deleteDossierAction($idDossier)
	{
		$this->get('app.security')->checkAccess('DOSSIER_DELETE');

	    $dossier = $this->getDoctrine()
	      ->getRepository('AppBundle:Dossier')
	      ->find($idDossier);
		
		$contact = $dossier->getContact();

		$em = $this->get('doctrine.orm.entity_manager');
		$em->remove($dossier);
		$em->flush();

      	$history = $this->get('app.history');
      	$history->init($this->getUser(),['id'=>$idDossier,'name'=>'Dossier'],'DELETE')
              	->log(true);

      	$this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

	    return $this->redirectToRoute('list_documents',array('idContact'=>$contact->getId()));
	}


	/**
	* @Route("/download/last/{fileName}", name="download_last_pdf")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function downloadLastPdfAction($fileName)
	{
		$response = new Response();
		$response->setContent(file_get_contents('pdf/last-'.$this->getUser()->getId().'.pdf'));
		$response->headers->set(
		   'Content-Type',
		   'application/force-download'
		);
		$response->headers->set('Content-disposition', 'filename='.$fileName.'.pdf');

		return $response;	
	}

	/**
	* @Route("/download/last/{type}/{fileName}", name="download_last_export")
	* @Security("has_role('ROLE_SPECTATOR')")
	*/
	public function downloadLastExportAction($fileName,$type)
	{
		$response = new Response();
		$response->setContent(file_get_contents('exports/last-'.$this->getUser()->getId().'.'.$type));
		$response->headers->set(
		   'Content-Type',
		   'application/force-download'
		);
		$response->headers->set('Content-disposition', 'filename='.$fileName.'.'.$type);

		return $response;	
	}

}