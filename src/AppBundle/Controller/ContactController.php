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
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function listContactsAction($idFilter,$page,$nb,$orderby,$order)
    {

      $this->get('app.security')->checkAccess('CONTACT_READ');

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
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function viewContactAction(Request $request,$idContact)
    {

      $this->get('app.security')->checkAccess('CONTACT_READ');

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
        $lstSuivis[$suiviContact->getDateCreation()->format('Y-m-d').'_'.$suiviContact->getId()] = $suiviContact;
      }
      
      foreach ($lstSuivisDossier as $suiviDossier) {
        $lstSuivis[$suiviDossier->getDateCreation()->format('Y-m-d').'_'.$suiviDossier->getId()] = $suiviDossier;
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
        $lstAllSuivis[$suiviContact->getDateCreation()->format('Y-m-d').'_'.$suiviContact->getId()] = $suiviContact;
      }
      
      foreach ($lstAllSuivisDossier as $suiviDossier) {
        $lstAllSuivis[$suiviDossier->getDateCreation()->format('Y-m-d').'_'.$suiviDossier->getId()] = $suiviDossier;
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

      $lateVignettes = $this->getDoctrine()
        ->getRepository('AppBundle:Vignette')
        ->findBy(array('datePaiement'=>null,'contact'=>$contact));

      // Documents
      //    Doc
      $document = new Document();
      $document->setContact($contact);
      $documentForm = $this->createForm(DocumentType::class, $document);
      $documentForm->handleRequest($request);

      //    Dossier
      $dossier = new Dossier();
      $dossier->setDateOuverture(new \DateTime());
      $dossierForm = $this->createForm(DossierType::class, $dossier);
      $dossierForm->handleRequest($request);

      $prevTrimestre = new \DateTime();
      $prevTrimestre->sub(new \DateInterval('PT9M'));

      // is offreDecouverte expiring
      $isExpiring = false;
      if($contact->getDateOffreDecouverte()){
          $isExpiring = $contact->getDateOffreDecouverte() < $prevTrimestre;
      }


      return $this->render('operateur/contacts/view-contact.html.twig', [
            'contact' => $contact,
            'suiviForm' => $suiviForm->createView(),
            'lstSuivis' => $lstSuivis,
            'lstAllSuivis' => $lstAllSuivis,
            'agrrs' => $agrrs,
            'obseques' => $obseques,
            'lateVignettes' => $lateVignettes,
            'vignetteForm' => $vignetteForm->createView(),
            'dossierForm' => $dossierForm->createView(),
            'documentForm' => $documentForm->createView(),
            'donForm' => $donForm->createView(),
            'isExpiring' => $isExpiring,
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
      $prevRentier = $contact->getIsRentier();
      $prevEnvoiIndiv = $contact->getIsEnvoiIndiv();
      $prevOffreDecouverte = $contact->getIsOffreDecouverte();
      $prevDateOffreDecouverte = $contact->getDateOffreDecouverte();

      $contactForm = $this->createForm(ContactFullEditionType::class, $contact);
         
      $contactForm->handleRequest($request);

      if($this->get('app.security')->hasAccess('CONTACT_WRITE')){
        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
          
          // check security : envoiIndiv
          if($prevEnvoiIndiv != $contact->getIsEnvoiIndiv()){
            if(!$this->get('app.security')->hasAccess('CONTACT_SET_ENVOI_INDIV')){
              $contact->setIsEnvoiIndiv($prevEnvoiIndiv);
              $this->get('session')->getFlashBag()->add('danger','Vous n\'avez pas la permission de modifier ce champ : Envoi Individuel');
              return $this->redirectToRoute('full_contact',['idContact'=>$contact->getId()]);
            }
          }

          // check security : offreDecouverte
          if($prevOffreDecouverte != $contact->getIsOffreDecouverte()){
            if(!$this->get('app.security')->hasAccess('CONTACT_SET_OFFRE_DECOUVERTE')){
              $contact->setIsOffreDecouverte($prevOffreDecouverte);
              $contact->setDateOffreDecouverte($prevDateOffreDecouverte);
              $this->get('session')->getFlashBag()->add('danger','Vous n\'avez pas la permission de modifier ce champ : Offre Découverte');
              return $this->redirectToRoute('full_contact',['idContact'=>$contact->getId()]);
            }
          }

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


          // case CA exists
          if($contact->getIsCA()&&$contact->getSection()){
            $membreCA = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->findOneBy(array('section'=>$contact->getSection(),'isCA'=>true));
            if($membreCA&&$membreCA->getId()!=$contact->getId()){
              $contact->setIsCA(false);
              $this->get('session')->getFlashBag()->add('danger', 'Erreur : Il y a déjà un membre CA de défini pour cette section. <a href="'.$this->generateUrl('view_contact',['idContact'=>$membreCA->getId()]).'">Accéder à son profil</a>');  
            }
          }

          $em = $this->get('doctrine.orm.entity_manager');
          $em->persist($contact);
          $em->flush(); 

          $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');    

          $history = $this->get('app.history');
          $history->init($this->getUser(),['id'=>$contact->getId(),'name'=>'Contact'],'UPDATE')
                  ->log(true); 
          
          // change isRentier
          if($prevRentier != $contact->getIsRentier()){
            if($contact->getIsRentier()){
              $this->get('app.suivi')->create($contact,'Est devenu "Destinataire rentier"');
            }else{
              $this->get('app.suivi')->create($contact,'A été retiré des "Desinataires rentier"');
            }
          }

          // change envoiIndiv
          if($prevEnvoiIndiv != $contact->getIsEnvoiIndiv()){
            if($contact->getIsEnvoiIndiv()){
              $this->get('app.suivi')->create($contact,'Est devenu "Envois individuel"');
            }else{
              $this->get('app.suivi')->create($contact,'N\'est plus en "Envois individuel"');
            }
          }

          // change offreDecouverte
          if($prevOffreDecouverte != $contact->getIsOffreDecouverte()){
            if($contact->getIsEnvoiIndiv()){
              $this->get('app.suivi')->create($contact,'Est devenu "Offre découverte"');
            }else{
              $this->get('app.suivi')->create($contact,'N\'est plus en "Offre découverte"');
            }
          }

        }
        if ($contactForm->isSubmitted() && !$contactForm->isValid()) {
          $this->get('app.tools')->handleFormErrors($contactForm);
        }
      }else{
        $this->get('session')->getFlashBag()->add('warning', 'Vous ne possédez pas les droits nécessaire à la modification du contact');
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
        $arrContacts[]=array('id'=>$contact->getId(),'nom'=>$contact->getNom(),'prenom'=>$contact->getPrenom(),'numAdh'=>$contact->getSection()->getId(),'path'=>$this->generateUrl('view_contact',array('idContact'=>$contact->getId())));
      }  

      if($request->request->get('joinSection')){
        $sections = $this->getDoctrine()
          ->getRepository('AppBundle:Section')
          ->search($request->request->get('txtSearch'));

        foreach ($sections as $section) {
          $arrContacts[]=array('id'=>$section->getId(),'nom'=>'Section : '.$section->getNom(),'prenom'=>'','numAdh'=>$section->getId(),'path'=>$this->generateUrl('view_section',array('idSection'=>$section->getId())));
        }
      }

      return new Response(json_encode($arrContacts)); 
    }

    /**
     * @Route("/contact/{idContact}/editer-membre-conjoint", name="contact_edit_conjoint")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function editConjointAction(Request $request,$idContact)
    {

      $this->get('app.security')->checkAccess('CONJOINT_WRITE');

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
                                          "Copie de la notification d'admission à Pôle-Emploi",
                                          "Copie du livret militaire",
                                          "Copie du livret de famille",
                                          "Justificatif de ressources pour la période du __________ au __________",
                                          "Copie des avis de paiement Pôle-Emploi depuis le début de l'année",
                                          "Autres :",
                          )),
             3 => array( 'title'=>"FIVA",
                        'fullTitle'=>"Objet : FIVA",
                        'values'=>array(
                                          "Décision CPAM de reconnaissance de la maladie professionnelle",
                                          "Copie notification de rente CPAM ou CAAA + rapport médical joint",
                                          "Copie carte d'indentité",
                                          "Bilan pneumologique ou compte rendu de la dernière EFR (Exploration Fonctionnelle Respiratoire)",
                                          "Compte rendu du dernier scanner ou radio des poumons",
                                          "Certificat médical du médecin mettant en évidence la maladie amiante",
                                          "Certificats de travail concernant les périodes d'exposition à l'amiante",
                                          "RIB",
                                          "Acte de décès",
                                          "Copie du livret de famille",
                                          "Pouvoir",
                                          "Autres",
                          )),
             4 => array( 'title'=>"Carte d'invalidité",
                        'fullTitle'=>"Objet : Carte d'invalidité Macaron Européen <br />
                        AAH (Allocation aux Adultes Handicapés) / TH (Travailleur Handicapé)",
                        'values'=>array(
                                          "Formulaire de demande complété et signé",
                                          "Formulaire médical dûment rempli par le médecin traitant et concluant à : un handicap supérieur à 80% (avec station debout pénible s'il y a lieu) / la difficulté à se déplacer (le cas échéant)",
                                          "1 photo d'identité pour la carte d'invalidité",
                                          "1 photo d'identité pour le macaron GIC",
                                          "Copie de la carte d'invalidité et du macaron échus",
                                          "Copie de la carte d'identité",
                                          "Copie du justificatif de domicile (facture gaz, électricité, téléphone...)",
                                          "1 CV",
                                          "Copie du contrat de travail",
                                          "Copie de la notification RTH ou AAH échue",
                          )),
             5 => array( 'title'=>"Pension CRAV et R.C",
                        'fullTitle'=>"Objet : Pension CRAV et R.C",
                        'values'=>array(
                                          "Copie de l'avis de non-imposition 2____",
                                          "Copie de la carte d'identité",
                                          "Copie de la notification de pension d'invalidité",
                                          "Copie de la notification d'admission aux ASSEDIC",
                                          "Copie du livret militaire",
                                          "Copie du livret de famille",
                                          "RIB",
                                          "Procuration",
                                          "Relevé de carrière",
                                          "Autres",
                          )),
             6 => array( 'title'=>"Pension d'invalidité",
                        'fullTitle'=>"Objet : Pension d'invalidité",
                        'values'=>array(
                                          "Formulaire complété",
                                          "Copie du livret de famille",
                                          "Attestation de l'employeur",
                                          "RIB",
                                          "Copie de l'avis d'imposition de l'année ______",
                                          "Copie de la carte d'identité",
                                          "Certificat médical",
                          )),
             7 => array( 'title'=>"Pension réversion",
                        'fullTitle'=>"Objet : Pension réversion",
                        'values'=>array(
                                          "Acte de décès",
                                          "Copie du livret de famille",
                                          "Extrait de l'acte de naissance",
                                          "RIB au nom de la veuve",
                                          "Revenu des 3 derniers mois du survivant, copie fiche de paie, attestation ASSEDIC, extraits bancaires",
                                          "Copie de l'avis de non imposition",
                                          "Attestations fiscales de pension personnelle + RC",
                                          "Copie du livret militaire",
                                          "Copie de la carte d'identité",
                          )),
             7 => array( 'title'=>"Adhésion à la mutuelle : frais médicaux",
                        'fullTitle'=>"Objet : Adhésion à la mutuelle : frais médicaux",
                        'values'=>array(
                                          "Formulaire complété",
                                          "Copie de l'attestation vitale de chacun des souscripteurs",
                                          "RIB",
                                          "Certificat de radiation de la précédente mutuelle",
                          )),
             8 => array( 'title'=>"Adhésion à la mutuelle : options",
                        'fullTitle'=>"Objet : Adhésion à la mutuelle : frais médicaux",
                        'values'=>array(
                                          "Formulaire complété",
                                          "Copie de l'attestation vitale de chacun des souscripteurs",
                                          "RIB",
                                          "Certificat de radiation de la précédente mutuelle",
                          )),
             9 => array( 'title'=>"Prévoyance Obsèques UNIAT-IRIAL 
                                    Versement capital décès",
                        'fullTitle'=>"Objet : Prévoyance Obsèques UNIAT-IRIAL",
                        'values'=>array(
                                          "Acte de décès",
                                          "Facture acquittée des Pompes Funèbres",
                                          "Certificat d'hérédité, à se procurer en Mairie",
                                          "Relevé d'identité bancaire ou postal, au nom de la personne qui a payé les obsèques",
                          )),                                                                                                                                     
          );

        return $this->render('operateur/contacts/pieces-a-fournir.html.twig',array(
            'contact' => $contact,
            'items' => $items
        ));
    }

    /**
     * @Route("/contact/{idContact}/suppression", name="delete_contact")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function deleteContactAction($idContact)
    {

      $this->get('app.security')->checkAccess('CONTACT_DELETE');

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
     * @Route("/contact/{idContact}/imprimer/etiquette", name="contact_print_etiquette")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function contactPrintEtiquetteAction($idContact)
    {
      $this->get('app.security')->checkAccess('CONTACT_ET_PRINT');

      $pdf = new PDF_Etiquette('L7163');
      $pdf->AddPage();

      $contact = $this->getDoctrine()
        ->getRepository('AppBundle:Contact')
        ->find($idContact);
      $text = sprintf("%s\n%s\n%s\n%s %s, %s", $contact->getNom().' '.$contact->getPrenom(), $contact->getAdresse(), $contact->getAdresseComp(), $contact->getCP(), $contact->getCommune(), $contact->getPays());
      $pdf->Add_Label(utf8_decode($text));

      $response = new Response();
      $response->setContent($pdf->Output());

      $response->headers->set(
         'Content-Type',
         'application/pdf'
      );

      return $response;
    }
    
    /**
     * @Route("/contact/listing/action", name="contact_action_listing")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function contactListingAction(Request $request)
    {

      $action = $request->request->get('action');
      switch ($action) {
        case 'DELETE_ITEMS':
          $this->get('app.security')->checkAccess('CONTACT_DELETE');
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
        case 'ETIQUETTES_DIP':
          $selection = $request->request->get('selection');
          $pdf = new PDF_Etiquette('L7163');
          $pdf->AddPage();
          
          $pdf->AddFont('Mistral');
          $pdf->SetFont('mistral','',40);

          foreach ($selection as $id) {
            $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($id);
            
            $cds = $this->getDoctrine()
                       ->getRepository('AppBundle:ContactDiplome')
                       ->findBy(array('contact'=>$contact));

            foreach ($cds as $contactDiplome) {
              $text = sprintf("%s %s", $contactDiplome->getContact()->getNom(), $contactDiplome->getContact()->getPrenom());
              $pdf->Add_Label(utf8_decode($text));

              $text = sprintf("%s", $contactDiplome->getContact()->getSection()?$contactDiplome->getContact()->getSection()->getNom():'');
              $pdf->Add_Label(utf8_decode($text));

              $text = sprintf("%s", $contactDiplome->getDateObtention()->format('d/m/Y'));
              $pdf->Add_Label(utf8_decode($text));
            }

          }
          $pdf->Output('F','pdf/last-'.$this->getUser()->getId().'.pdf');
          $path = $this->generateUrl('download_last_pdf',['fileName'=>'export-etiquettes-diplomes']);
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
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function addContactAction(Request $request)
    {

      $this->get('app.security')->checkAccess('CONTACT_WRITE');

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

          $this->get('session')->getFlashbag()->add('danger','Ajout impossible : ce contact existe déjà. (nom, prénom, téléphone et adresse similaires) <a href="'.$this->generateUrl('view_contact',['idContact'=>$doubloon->getId()]).'">Accéder à sa fiche</a>');

          return $this->render('operateur/contacts/full-contact.html.twig', [
            'contact' => $contact,
            'isInsert' => true,
            'contactForm' => $contactForm->createView(),
          ]);
        }

        $contact->setDateEntree(new \DateTime());

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($contact);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

        $history = $this->get('app.history');
        $history->init($this->getUser(),['id'=>$contact->getId(),'name'=>'Contact'],'INSERT')
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
