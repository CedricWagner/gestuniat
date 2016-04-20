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
use AppBundle\Form\DonType;
use AppBundle\Form\VignetteType;
use AppBundle\Form\DossierType;
use AppBundle\Form\DocumentType;


class ContactController extends Controller
{
    /**
     * @Route("/contact/liste/{idFilter}/{page}/{nb}", name="list_contacts", defaults={"idFilter" = 0,"page" = 1,"nb" = 0})
     * @Security("has_role('ROLE_USER')")
     */
    public function listContactsAction($idFilter,$page,$nb)
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
                          ->findBy(array(),array('label'=>'ASC'));

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
            'contacts' => $contacts,
            'pagination' => array('count'=>count($contacts),'nb'=>$nb,'page'=>$page),
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

        return  $this->redirectToRoute('view_contact', array('idContact' => $contact->getId()));
      }

      $lstSuivis = $this->getDoctrine()
        ->getRepository('AppBundle:Suivi')
        ->findBy(array('operateur'=>$this->getUser(),'contact'=>$contact,'isOk'=>false),array('dateCreation'=>'DESC'),5);


      $lstAllSuivis = $this->getDoctrine()
        ->getRepository('AppBundle:Suivi')
        ->findBy(array('operateur'=>$this->getUser(),'contact'=>$contact),array('dateCreation'=>'DESC'));

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
     * @Security("has_role('ROLE_USER')")
     */
    public function fullContactAction(Request $request, $idContact)
    {
      $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($idContact);

      $contactForm = $this->createForm(ContactFullEditionType::class, $contact);

      $contactForm->handleRequest($request);

      if ($contactForm->isSubmitted() && $contactForm->isValid()) {
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($contact);
        $em->flush();      
      }

      return $this->render('operateur/contacts/full-contact.html.twig', [
            'contact' => $contact,
            'isInsert' => false,
            'contactForm' => $contactForm->createView(),
        ]);
    }

    /**
     * @Route("/search/contact", name="contact_search")
     * @Security("has_role('ROLE_USER')")
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

        $contact->setMembreConjoint($conjoint);

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($contact);
        $em->flush(); 

      }elseif ($type=='existing' && $request->request->get('idMembreConjoint')){

        $conjoint = $this->getDoctrine()
          ->getRepository('AppBundle:Contact')
          ->find($request->request->get('idMembreConjoint'));

        $contact->setMembreConjoint($conjoint);
        $conjoint->setMembreConjoint($contact);

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($contact);
        $em->flush(); 

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($conjoint);
        $em->flush(); 

      }else{

        if ($contact->getMembreConjoint()) {

          $conjoint = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->find($contact->getMembreConjoint()->getId());

          $contact->setMembreConjoint(null);
          $conjoint->setMembreConjoint(null);

          $em = $this->get('doctrine.orm.entity_manager');
          $em->persist($contact);
          $em->flush(); 

          $em = $this->get('doctrine.orm.entity_manager');
          $em->persist($conjoint);
          $em->flush(); 

        }

      }

      return $this->redirectToRoute('view_contact',array('idContact'=>$contact->getId()));

    }


    /**
     * @Route("/contact/{idContact}/kit-adhesion", name="kit_adh_contact")
     * @Security("has_role('ROLE_USER')")
     */
    public function kitAdhContactAction($idContact)
    {
       $contact = $this->getDoctrine()
        ->getRepository('AppBundle:Contact')
        ->find($idContact);

        return $this->render('operateur/contacts/kit-adhesion.html.twig',array(
            'contact' => $contact
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
    
      $contact = $procuration->getContact();

      $em = $this->get('doctrine.orm.entity_manager');
      $em->remove($contact);
      $em->flush();

      return $this->redirectToRoute('list_contacts');
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

      $contactForm->handleRequest($request);

      if ($contactForm->isSubmitted() && $contactForm->isValid()) {
        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($contact);
        $em->flush();
        return $this->redirectToRoute('view_contact',array('idContact'=>$contact->getId()));
      }

      return $this->render('operateur/contacts/full-contact.html.twig', [
            'contact' => $contact,
            'isInsert' => true,
            'contactForm' => $contactForm->createView(),
        ]);
    }

}
