<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\ContactFullEditionType;
use AppBundle\Form\SuiviDefaultType;
use AppBundle\Entity\Suivi;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Don;
use AppBundle\Entity\Vignette;
use AppBundle\Entity\Dossier;
use AppBundle\Entity\Document;
use AppBundle\Entity\StatutJuridique;
use AppBundle\Entity\StatutMatrimonial;
use AppBundle\Form\DonType;
use AppBundle\Form\VignetteType;
use AppBundle\Form\DossierType;
use AppBundle\Form\DocumentType;
use AppBundle\Utils\FPDF\FPDF;
use AppBundle\Utils\FPDF\templates\Etiquette as PDF_Etiquette;
use AppBundle\Utils\FPDF\templates\DefaultModel as PDF_DefaultModel;


class ContactController extends Controller
{
    /**
     * @Route("/contact/liste/{idFilter}/{page}/{nb}/{orderby}-{order}", name="list_contacts", defaults={"idFilter" = 0,"page" = 1,"nb" = 0, "orderby"= "numAdh","order"= "ASC"})
     * @Security("has_role('ROLE_USER')")
     */
    public function listContactsAction($idFilter,$page,$nb,$orderby,$order)
    {

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
                          ->findBy(array('operateur'=>$this->getUser(),'contexte'=>'contact'),array('label'=>'ASC'));

        $statutsJuridiques = $this->getDoctrine()
                              ->getRepository('AppBundle:StatutJuridique')
                              ->findAll();

        $fonctionsGroupement = $this->getDoctrine()
                              ->getRepository('AppBundle:FonctionGroupement')
                              ->findAll();

        $fonctionsSection = $this->getDoctrine()
                              ->getRepository('AppBundle:FonctionSection')
                              ->findAll();

        $diplomes = $this->getDoctrine()
                              ->getRepository('AppBundle:Diplome')
                              ->findAll();

        $sections = $this->getDoctrine()
                              ->getRepository('AppBundle:Section')
                              ->findAll();

        if($currentFilter){
          $contacts = $this->getDoctrine()
                      ->getRepository('AppBundle:Contact')
                      ->findByFilter($filtreValeurs,$page,$nb);
        }else{
          $contacts = $this->getDoctrine()
                      ->getRepository('AppBundle:Contact')
                      ->findAllWithPagination($page,$nb);
        }

        return $this->render('operateur/contacts/contacts.html.twig', [
            'filtresPerso' => $filtresPerso,
            'statutsJuridiques' => $statutsJuridiques,
            'fonctionsGroupement' => $fonctionsGroupement,
            'fonctionsSection' => $fonctionsSection,
            'diplomes' => $diplomes,
            'currentFilter' => $currentFilter,
            'items' => $contacts,
            'sections' => $sections,
            'pagination' => array('count'=>count($contacts),'nb'=>$nb,'page'=>$page,'orderby'=>$orderby,'order'=>$order),
        ]);
    }

    /**
     * @Route("/contact/{idContact}", name="view_contact"), requirements={
     *     "idContact": "\d+"
     * })
     * @Security("has_role('ROLE_USER')")
     */
    public function viewContactAction(Request $request,$idContact)
    {
      // Contact
      $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($idContact);

      // Suivis
      $suivi = new Suivi();

      $suiviForm = $this->createForm(SuiviDefaultType::class, $suivi);
      $suiviForm->handleRequest($request);

      if ($suiviForm->isSubmitted() && $suiviForm->isValid()) {
                
        $datetime = new \DateTime();

        $suivi->setDateCreation($datetime);
        $suivi->setDateEdition($datetime);
        $suivi->setOperateur($this->getUser());
        $suivi->setContact($contact);

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($suivi);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

        return  $this->redirectToRoute('view_contact', array('idContact' => $contact->getId()));
      }
      if ($suiviForm->isSubmitted() && !$suiviForm->isValid()) {
        $this->get('app.tools')->handleFormErrors($suiviForm);
      }

      $lstSuivisContact = $this->getDoctrine()
        ->getRepository('AppBundle:Suivi')
        ->findBy(array('contact'=>$contact,'isOk'=>false),array('dateCreation'=>'ASC'),5);

      $lstSuivisDossier = $this->getDoctrine()
        ->getRepository('AppBundle:Suivi')
        ->findByDossier($contact,false,5);

      $lstSuivis = array();

      foreach ($lstSuivisContact as $suiviContact) {
        $lstSuivis[$suiviContact->getDateCreation()->format('Y-m-d').'_'.$suivi->getId()] = $suiviContact;
      }
      
      foreach ($lstSuivisDossier as $suiviDossier) {
        $lstSuivis[$suiviDossier->getDateCreation()->format('Y-m-d').'_'.$suivi->getId()] = $suiviDossier;
      }

      ksort($lstSuivis);

      $lstAllSuivisContact = $this->getDoctrine()
        ->getRepository('AppBundle:Suivi')
        ->findBy(array('contact'=>$contact),array('dateCreation'=>'ASC'));

      $lstAllSuivisDossier = $this->getDoctrine()
        ->getRepository('AppBundle:Suivi')
        ->findByDossier($contact,true);

      $lstAllSuivis = array();

      foreach ($lstAllSuivisContact as $suiviContact) {
        $lstAllSuivis[$suiviContact->getDateCreation()->format('Y-m-d').'_'.$suivi->getId()] = $suiviContact;
      }
      
      foreach ($lstAllSuivisDossier as $suiviDossier) {
        $lstAllSuivis[$suiviDossier->getDateCreation()->format('Y-m-d').'_'.$suivi->getId()] = $suiviDossier;
      }

      ksort($lstAllSuivis);

      if ($contact->getMembreConjoint()) {
        $conjoint = $this->getDoctrine()
          ->getRepository('AppBundle:Contact')
          ->find($contact->getMembreConjoint()->getId());
      }else{
        //shortcut
        $conjoint = $contact;
      }

      $agrrs = $this->getDoctrine()
        ->getRepository('AppBundle:ContratPrevoyance')
        ->findByContactAndConjoint($contact,$conjoint);


      $obseques = $this->getDoctrine()
        ->getRepository('AppBundle:ContratPrevObs')
        ->findByContactAndConjoint($contact,$conjoint);

      // Contributions
      //    Don
      $don = new Don();
      $donForm = $this->createForm(DonType::class, $don);
      $donForm->handleRequest($request);

      //    Vignette
      $vignette = new Vignette();
      $vignette->setContact($contact);
      $vignetteForm = $this->createForm(VignetteType::class, $vignette);
      $vignetteForm->handleRequest($request);

      // Documents
      //    Doc
      $document = new Document();
      $documentForm = $this->createForm(DocumentType::class, $document);
      $documentForm->handleRequest($request);

      //    Dossier
      $dossier = new Dossier();
      $dossierForm = $this->createForm(DossierType::class, $dossier);
      $dossierForm->handleRequest($request);


      return $this->render('operateur/contacts/view-contact.html.twig', [
            'contact' => $contact,
            'suiviForm' => $suiviForm->createView(),
            'lstSuivis' => $lstSuivis,
            'lstAllSuivis' => $lstAllSuivis,
            'agrrs' => $agrrs,
            'obseques' => $obseques,
            'vignetteForm' => $vignetteForm->createView(),
            'dossierForm' => $dossierForm->createView(),
            'documentForm' => $documentForm->createView(),
            'donForm' => $donForm->createView(),
        ]);
    }


    /**
     * @Route("/contact/{idContact}/profil-complet", name="full_contact")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function fullContactAction(Request $request, $idContact)
    {
      $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($idContact);
      $prevDateDeces = $contact->getDateDeces();

      $contactForm = $this->createForm(ContactFullEditionType::class, $contact);
         

      $contactForm->handleRequest($request);

      if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
          
          // case décès
          if($prevDateDeces == null && $contact->getDateDeces() != null){
            if($contact->getMembreConjoint()){

              $conjoint = $contact->getMembreConjoint();
              if($conjoint->getStatutJuridique()->getId()!=StatutJuridique::getIdStatutAdherent()){
                $conjoint->setStatutJuridique($this->getDoctrine()->getRepository('AppBundle:StatutJuridique')->find(StatutJuridique::getIdPoursuiteAdh()));
              }
              $conjoint->setStatutMatrimonial($this->getDoctrine()->getRepository('AppBundle:StatutMatrimonial')->find(StatutMatrimonial::getIdVeuf()));


              $em = $this->get('doctrine.orm.entity_manager');
              $em->persist($conjoint);
              $em->flush(); 
            }
            $contact->setStatutJuridique($this->getDoctrine()->getRepository('AppBundle:StatutJuridique')->find(StatutJuridique::getIdDeces()));
          }

          $em = $this->get('doctrine.orm.entity_manager');
          $em->persist($contact);
          $em->flush(); 

          $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');    

          $history = $this->get('app.history');
          $history->init($this->getUser(),['id'=>$idContact,'name'=>'Contact'],'UPDATE')
                  ->log(true); 

        }
        if ($contactForm->isSubmitted() && !$contactForm->isValid()) {
          $this->get('app.tools')->handleFormErrors($contactForm);
        }
      }else{
        $this->get('session')->getFlashBag()->add('danger', 'Vous ne possédez pas les droits nécessaires pour cette action');    
      }

      return $this->render('operateur/contacts/full-contact.html.twig', [
            'contact' => $contact,
            'isInsert' => false,
            'contactForm' => $contactForm->createView(),
        ]);
    }

    /**
     * @Route("/search/contact", name="contact_search")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function ajaxContactSearchAction(Request $request)
    {
      
      if($request->request->get('idContact')){
        $exclude = $this->getDoctrine()
          ->getRepository('AppBundle:Contact')
          ->find($request->request->get('idContact'));
      }else{
        $exclude = false;
      }

      $contacts = $this->getDoctrine()
        ->getRepository('AppBundle:Contact')
        ->search($request->request->get('txtSearch'),$exclude);

      $arrContacts = array();
      foreach ($contacts as $contact) {
        $arrContacts[]=array('id'=>$contact->getId(),'nom'=>$contact->getNom(),'prenom'=>$contact->getprenom(),'numAdh'=>$contact->getNumAdh(),'path'=>$this->generateUrl('view_contact',array('idContact'=>$contact->getId())));
      }  

      return new Response(json_encode($arrContacts)); 
    }

    /**
     * @Route("/contact/{idContact}/editer-membre-conjoint", name="contact_edit_conjoint")
     * @Security("has_role('ROLE_USER')")
     */
    public function editConjointAction(Request $request,$idContact)
    {

      $contact = $this->getDoctrine()
        ->getRepository('AppBundle:Contact')
        ->find($idContact);

      $type = $request->request->get('rbTypeContact');

      if($type=='new'){
        //add a new contact

        $conjoint = new Contact();
        $conjoint->setNom($request->request->get('txtNom'));
        $conjoint->setPrenom($request->request->get('txtPrenom'));
        
        $civilite =  $this->getDoctrine()
          ->getRepository('AppBundle:Civilite')
          ->find($request->request->get('selCivilite'));


        $conjoint->setCivilite($civilite);

        //setting default values
        $conjoint->setIsRentier(false);
        $conjoint->setIsBI(false);
        $conjoint->setIsCourrier(false);
        $conjoint->setIsEnvoiIndiv(false);
        $conjoint->setIsDossierPaye(false);
        $conjoint->setIsCA(false);
        $conjoint->setIsoffreDecouverte(false);
        $conjoint->setIsActif(true);

        $today = new \DateTime();
        $conjoint->setDateEntree($today);
        
        $defaultStatut =  $this->getDoctrine()
          ->getRepository('AppBundle:StatutJuridique')
          ->findOneBy(array('label'=>'Non-membre'));
        $conjoint->setStatutJuridique($defaultStatut);
        
        $nextNum =  $this->getDoctrine()
          ->getRepository('AppBundle:Contact')
          ->findMaxNumAdh();
        $conjoint->setNumAdh($nextNum+1);
        $conjoint->setMembreConjoint($contact);

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($conjoint);
        $em->flush(); 

        $suivi = new Suivi();
        $suivi->setOperateur($this->getUser())
              ->setTexte('Ajout d\'un membre conjoint (création)')
              ->setDateCreation($today)
              ->setIsOk(true)
              ->setContact($contact);
        $em->persist($suivi);
        $em->flush();

        $suivi = new Suivi();
        $suivi->setOperateur($this->getUser())
              ->setTexte('Création')
              ->setDateCreation($today)
              ->setIsOk(true)
              ->setContact($conjoint);
        $em->persist($suivi);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

        $contact->setMembreConjoint($conjoint);

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($contact);
        $em->flush(); 

      }elseif ($type=='existing' && $request->request->get('idMembreConjoint')){
        //replace conjoint

        $conjoint = $this->getDoctrine()
          ->getRepository('AppBundle:Contact')
          ->find($request->request->get('idMembreConjoint'));

        $contact->setMembreConjoint($conjoint);
        $conjoint->setMembreConjoint($contact);

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($contact);
        $em->flush();

        $suivi = new Suivi();
        $suivi->setOperateur($this->getUser())
              ->setTexte('Ajout d\'un membre conjoint (contact existant)')
              ->setDateCreation(new \DateTime())
              ->setIsOk(true)
              ->setContact($contact);
        $em->persist($suivi);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($conjoint);
        $em->flush(); 

      }else{
        //remove conjoint

        if ($contact->getMembreConjoint()) {

          $conjoint = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->find($contact->getMembreConjoint()->getId());

          $contact->setMembreConjoint(null);
          $conjoint->setMembreConjoint(null);

          $em = $this->get('doctrine.orm.entity_manager');
          $em->persist($contact);
          $em->flush(); 


          $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

          $em = $this->get('doctrine.orm.entity_manager');
          $em->persist($conjoint);
          $em->flush(); 
          
          $suivi = new Suivi();
          $suivi->setOperateur($this->getUser())
                ->setTexte('Suppression du membre conjoint')
                ->setDateCreation(new \DateTime())
                ->setIsOk(true)
                ->setContact($contact);
          $em->persist($suivi);
          $em->flush();

        }

      }

      return $this->redirectToRoute('view_contact',array('idContact'=>$contact->getId()));

    }


    /**
     * @Route("/contact/{idContact}/kit-adhesion", name="kit_adh_contact")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function kitAdhContactAction($idContact)
    {
       $contact = $this->getDoctrine()
        ->getRepository('AppBundle:Contact')
        ->find($idContact);


        return $this->render('operateur/contacts/kit-adhesion.html.twig',array(
            'contact' => $contact,
          ));
    }

    /**
     * @Route("/contact/{idContact}/pieces-a-fournir", name="pieces_a_fournir_contact")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function piecesAFournirAction($idContact)
    {
       $contact = $this->getDoctrine()
        ->getRepository('AppBundle:Contact')
        ->find($idContact);

        $items = array(
            1 => array( 'title'=>"Adhésion à la mutuelle",
                        'fullTitle'=>"Objet : Adhésion à la mutuelle <br />        - Frais médicaux<br />        - Options",
                        'values'=>array(
                                          "Formulaire complété",
                                          "Copie de l'attestation vitale de chacun des sousscripteurs",
                                          "Relevé d'identité bancaire",
                                          "Certificat de radiation de la précédente mutuelle",
                                          "Carte vitale",
                          )),
            2 => array( 'title'=>"ATS",
                        'fullTitle'=>"Objet : ATS",
                        'values'=>array(
                                          "Copie avis de non-imposition 2___",
                                          "Copie carte d'identité",
                          )),
          );

        return $this->render('operateur/contacts/pieces-a-fournir.html.twig',array(
            'contact' => $contact,
            'items' => $items
        ));
    }

    /**
     * @Route("/contact/{idContact}/suppression", name="delete_contact")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteContactAction($idContact)
    {

      $contact = $this->getDoctrine()
        ->getRepository('AppBundle:Contact')
        ->find($idContact);

      $contact->setIsActif(false);

      $em = $this->get('doctrine.orm.entity_manager');
      $em->persist($contact);
      $em->flush();

      $this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

      $history = $this->get('app.history');
      $history->init($this->getUser(),['id'=>$idContact,'name'=>'Contact'],'DELETE')
              ->log(true); 

      return $this->redirectToRoute('list_contacts');
    }

    /**
     * @Route("/contact/listing/action", name="contact_action_listing")
     * @Security("has_role('ROLE_USER')")
     */
    public function contactListingAction(Request $request)
    {

      $action = $request->request->get('action');
      switch ($action) {
        case 'DELETE_ITEMS':
          $selection = $request->request->get('selection');
          foreach ($selection as $id) {
            $this->deleteContactAction($id);
          }
          $path = $this->generateUrl('list_contacts');
          break;
        case 'ETIQUETTES':
          $selection = $request->request->get('selection');
          $pdf = new PDF_Etiquette('L7163');
          $pdf->AddPage();
          foreach ($selection as $id) {
            $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($id);
            $text = sprintf("%s\n%s\n%s\n%s %s, %s", $contact->getNom().' '.$contact->getPrenom(), $contact->getAdresse(), $contact->getAdresseComp(), $contact->getCP(), $contact->getCommune(), $contact->getPays());
            $pdf->Add_Label(utf8_decode($text));
          }
          $pdf->Output('F','pdf/last-'.$this->getUser()->getId().'.pdf');
          $path = $this->generateUrl('download_last_pdf',['fileName'=>'export-etiquettes']);
          break;
        case 'EXPORT':
          $selection = $request->request->get('selection');
          $csv = $this->get('app.csvgenerator');
          $csv->setName('export_liste-contacts');
          $csv->addLine(array('Nom','Prénom','Num','Statut','Adresse','CP','Commune','Section','Fonction (section)','Fonction (groupement)','Date d\'entrée'));

          foreach ($selection as $id) {
            $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($id);
            $fields = array(
              $contact->getNom(),
              $contact->getPrenom(),
              $contact->getNumAdh(),
              $contact->getStatutJuridique()->getLabel(),
              $contact->getAdresse(),
              $contact->getCp(),
              $contact->getCommune(),
              $contact->getSection()?$contact->getSection()->getNom():'',
              $contact->getFonctionSection()?$contact->getFonctionSection()->getLabel():'',
              $contact->getFonctionGroupement()?$contact->getFonctionGroupement()->getLabel():'',
              $contact->getDateEntree()?$contact->getDateEntree()->format('d/m/Y'):'',
            );
            $csv->addLine($fields);
          }
          $csv->generateContent('exports/last-'.$this->getUser()->getId().'.csv');
          
          $path = $this->generateUrl('download_last_export',['fileName'=>'export_liste-contacts','type'=>'csv']);
          break;
        default:
          
          break;
      }

      return new Response($path);
    }

    /**
     * @Route("/nouveau-contact", name="add_contact")
     * @Security("has_role('ROLE_USER')")
     */
    public function addContactAction(Request $request)
    {
      $contact = new Contact();

      $contactForm = $this->createForm(ContactFullEditionType::class, $contact);

      $maxNumAdh = $this->getDoctrine()
        ->getRepository('AppBundle:Contact')
        ->findMaxNumAdh();

      $maxNumAdh++;

      $contact->setNumAdh($maxNumAdh);
      $contact->setIsActif(true);

      $contactForm->handleRequest($request);

      if ($contactForm->isSubmitted() && $contactForm->isValid()) {
        
        //check for doubloons        
        $doubloon = $this->getDoctrine()
          ->getRepository('AppBundle:Contact')
          ->findBy(array(
            'nom'=>$contact->getNom(),
            'prenom'=>$contact->getPrenom(),
            'telFixe'=>$contact->getTelFixe(),
            'adresse'=>$contact->getAdresse(),
          ));

        if (sizeof($doubloon)) {
          $doubloon = $doubloon[0];

          $this->get('session')->getFlashbag()->add('danger','Ajout impossible : ce contact existe déjà. <a href="'.$this->generateUrl('view_contact',['idContact'=>$doubloon->getId()]).'">Accéder à sa fiche</a>');

          return $this->render('operateur/contacts/full-contact.html.twig', [
            'contact' => $contact,
            'isInsert' => true,
            'contactForm' => $contactForm->createView(),
          ]);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($contact);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

        $history = $this->get('app.history');
        $history->init($this->getUser(),['id'=>$idContact,'name'=>'Contact'],'INSERT')
                ->log(true); 

        return $this->redirectToRoute('view_contact',array('idContact'=>$contact->getId()));
      }
      if ($contactForm->isSubmitted() && !$contactForm->isValid()) {
        $this->get('app.tools')->handleFormErrors($contactForm);
      }

      return $this->render('operateur/contacts/full-contact.html.twig', [
            'contact' => $contact,
            'isInsert' => true,
            'contactForm' => $contactForm->createView(),
        ]);
    }
}
