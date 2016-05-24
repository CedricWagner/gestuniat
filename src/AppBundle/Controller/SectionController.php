<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Suivi;
use AppBundle\Entity\Patrimoine;
use AppBundle\Entity\Section;
use AppBundle\Entity\Permanence;
use AppBundle\Entity\AssembleeGenerale;
use AppBundle\Form\SuiviDefaultType;
use AppBundle\Form\PatrimoineType;
use AppBundle\Form\SectionFullType;
use AppBundle\Form\PermanenceType;
use AppBundle\Form\AssembleeGeneraleType;

class SectionController extends Controller
{

    /**
     * @Route("/section/liste/{idFilter}/{page}/{nb}", name="list_sections", defaults={"idFilter" = 0,"page" = 1,"nb" = 0})
     * @Security("has_role('ROLE_USER')")
     */
    public function listSectionsAction($idFilter,$page,$nb)
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
                  ->findBy(array('operateur'=>$this->getUser(),'contexte'=>'section'),array('label'=>'ASC'));

        if($currentFilter){
			$sections = $this->getDoctrine()
			  ->getRepository('AppBundle:Section')
			  ->findByFilter($filtreValeurs,$page,$nb);
        }else{
			$sections = $this->getDoctrine()
			  ->getRepository('AppBundle:Section')
			  ->findAllWithPagination($page,$nb);
        }

        return $this->render('operateur/sections/sections.html.twig', [
            'filtresPerso' => $filtresPerso,
            'currentFilter' => $currentFilter,
            'items' => $sections,
            'pagination' => array('count'=>count($sections),'nb'=>$nb,'page'=>$page),
        ]);

    }


    /**
     * @Route("/section/{idSection}/accueil", name="view_section"), requirements={
     *     "idSection": "\d+"
     * })
     * @Security("has_role('ROLE_USER')")
     */
    public function viewSectionAction(Request $request, $idSection)
    {
		$datetime = new \DateTime();

        // Section
        $section = $this->getDoctrine()
          ->getRepository('AppBundle:Section')
          ->find($idSection);

        // Suivis
        $suivi = new Suivi();

        $suiviForm = $this->createForm(SuiviDefaultType::class, $suivi);
        $suiviForm->handleRequest($request);

        if ($suiviForm->isSubmitted() && $suiviForm->isValid()) {
                    

			$suivi->setDateCreation($datetime);
			$suivi->setDateEdition($datetime);
			$suivi->setOperateur($this->getUser());
			$suivi->setSection($section);

			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($suivi);
			$em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
        }
        if ($suiviForm->isSubmitted() && !$suiviForm->isValid()) {
          $this->get('app.tools')->handleFormErrors($suiviForm);
        }

		$lstSuivis = $this->getDoctrine()
			->getRepository('AppBundle:Suivi')
			->findBy(array('section'=>$section,'isOk'=>false),array('dateCreation'=>'ASC'),5);

		$lstAllSuivis = $this->getDoctrine()
			->getRepository('AppBundle:Suivi')
			->findBy(array('section'=>$section),array('dateCreation'=>'ASC'));

        $patrimoine = new Patrimoine();
        $patrimoine->setAnnee($datetime->format('Y'));
        $newPatrimoineForm = $this->createForm(PatrimoineType::class, $patrimoine);

        $permanence = $this->getDoctrine()
            ->getRepository('AppBundle:Permanence')
            ->findOneBy(array('section'=>$section));
        
        $assembleeGenerale = $this->getDoctrine()
            ->getRepository('AppBundle:AssembleeGenerale')
            ->findOneBy(array('section'=>$section),array('date'=>'DESC'));
            
        $newPermanence = new Permanence();
        $newPermanenceForm = $this->createForm(PermanenceType::class, $newPermanence);
            
        $newAssembleeGenerale = new AssembleeGenerale();
        $newAssembleeGeneraleForm = $this->createForm(AssembleeGeneraleType::class, $newAssembleeGenerale);

        $assembleeGeneraleForm = $this->createForm(AssembleeGeneraleType::class, $assembleeGenerale);
       
        $permanenceForm = $this->createForm(PermanenceType::class, $permanence);

        $patrimoineForms = array();
        $patrimoines = array();
        // only display the 5 last years
        $displayedYears = array(
                $datetime->format('Y')-5 => $datetime->format('Y')-5,
                $datetime->format('Y')-4 => $datetime->format('Y')-4,
                $datetime->format('Y')-3 => $datetime->format('Y')-3,
                $datetime->format('Y')-2 => $datetime->format('Y')-2,
                $datetime->format('Y')-1 => $datetime->format('Y')-1,
                $datetime->format('Y') => $datetime->format('Y'),
            );

        for ($i=2010; $i <= $datetime->format('Y'); $i++) { 
            $_patrimoine = $this->getDoctrine()
                ->getRepository('AppBundle:Patrimoine')
                ->findOneBy(array('section'=>$section,'annee'=>$i));
            if(!$_patrimoine){   
                $_patrimoine = new Patrimoine();
                $_patrimoine->setAnnee($i);
                $_patrimoine->setValeur(0);
                $_patrimoine->setInterets(0);
            }
            $patrimoineForm = $this->createForm(PatrimoineType::class, $_patrimoine,array(
                'action'=> $this->generateUrl('save_patrimoine',['idSection'=>$section->getId()]).'?idPatrimoine='.$_patrimoine->getId(),
            ));
            $patrimoineForms[$i] = $patrimoineForm->createView();
            $patrimoines[$i] = $_patrimoine;
            $allYears[]=$i;
        }

        $currentTimbres = $this->getDoctrine()
            ->getRepository('AppBundle:RemiseTimbre')
            ->findBy(array(
                'section'=>$section,
                'annee'=>$datetime->format('Y')-1
                ));

        $etatTimbres = array('emis'=>0,'remis'=>0,'payes'=>0);    
        foreach ($currentTimbres as $timbre) {
            $etatTimbres['emis'] += $timbre->getNbEmis();
            $etatTimbres['remis'] += $timbre->getNbRemis();
            $etatTimbres['payes'] += $timbre->getNbPayes();
        }

		return $this->render('operateur/sections/view-section.html.twig', [
            'section' => $section,
            'displayedYears' => $displayedYears,
            'allYears' => $allYears,
            'patrimoines' => $patrimoines,
            'permanence' => $permanence,
            'assembleeGenerale' => $assembleeGenerale,
            'newPermanenceForm' => $newPermanenceForm->createView(),
            'assembleeGeneraleForm' => $assembleeGeneraleForm->createView(),
            'newAssembleeGeneraleForm' => $newAssembleeGeneraleForm->createView(),
            'permanenceForm' => $permanenceForm->createView(),
		    'patrimoineForms' => $patrimoineForms,
            'suiviForm' => $suiviForm->createView(),
		    'newPatrimoineForm' => $newPatrimoineForm->createView(),
		    'lstSuivis' => $lstSuivis,
            'lstAllSuivis' => $lstAllSuivis,
		    'etatTimbres' => $etatTimbres,
		]);

    }

    /**
     * @Route("/section/{idSection}/profil-complet", name="full_section")
     * @Security("has_role('ROLE_USER')")
     */
    public function fullSectionAction(Request $request, $idSection)
    {
        $section = $this->getDoctrine()
        ->getRepository('AppBundle:Section')
        ->find($idSection);

        $sectionForm = $this->createForm(SectionFullType::class, $section);
        $sectionForm->handleRequest($request);

        if ($sectionForm->isSubmitted() && $sectionForm->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($section);
            $em->flush(); 

            $history = $this->get('app.history');
            $history->init($this->getUser(),['id'=>$idSection,'name'=>'Section'],'UPDATE')
                    ->log(true);  

            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');    
        }
        if ($sectionForm->isSubmitted() && !$sectionForm->isValid()) {
          $this->get('app.tools')->handleFormErrors($sectionForm);
        }

        return $this->render('operateur/sections/full-section.html.twig', [
            'section' => $section,
            'isInsert' => false,
            'sectionForm' => $sectionForm->createView(),
        ]);

    }

    /**
     * @Route("/section/{idSection}/liste-adherents", name="list_contacts_section")
     * @Security("has_role('ROLE_USER')")
     */
    public function listAdherentsAction($idSection)
    {

        $fields = [
            ['type'=>'select','name'=>'selSection','value'=>$idSection],
            ['type'=>'checkbox','name'=>'cbStatut','value'=>2],
        ];

        $filterController = $this->get('app.filter.controller');
        $result = $filterController->registerFilter($fields,'Derniers paramètres','contact');

        $filter = $result['filtrePerso'];

        return $this->redirectToRoute('list_contacts',array('idFilter'=>$filter->getId()));
    }

    /**
     * @Route("/section/ajout", name="add_section")
     * @Security("has_role('ROLE_USER')")
     */
    public function addSectionAction(request $request)
    {

        $datetime = new \DateTime();

        $section = new Section();

        $sectionForm = $this->createForm(SectionFullType::class, $section);
        $sectionForm->handleRequest($request);

        if ($sectionForm->isSubmitted() && $sectionForm->isValid()) {
            $section->setDateCreation($datetime);
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($section);
            $em->flush();

            $history = $this->get('app.history');
            $history->init($this->getUser(),['id'=>$section->getId(),'name'=>'Section'],'INSERT')
                ->log(true); 

            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

            return $this->redirectToRoute('view_section',array('idSection'=>$section->getId()));  
        }
        if ($sectionForm->isSubmitted() && !$sectionForm->isValid()) {
          $this->get('app.tools')->handleFormErrors($sectionForm);
        }

        return $this->render('operateur/sections/full-section.html.twig', [
            'section' => $section,
            'isInsert' => true,
            'sectionForm' => $sectionForm->createView(),
        ]);


    }

     /**
     * @Route("/section/listing/action", name="section_action_listing")
     * @Security("has_role('ROLE_USER')")
     */
    public function sectionListingAction(Request $request)
    {

      $action = $request->request->get('action');
      switch ($action) {
        case 'DELETE_ITEMS':
          $selection = $request->request->get('selection');
          foreach ($selection as $id) {
            $this->deleteSectionAction($id);
          }
          $path = $this->generateUrl('list_sections');
          break;
        case 'EXPORT':
          $selection = $request->request->get('selection');
          $csv = $this->get('app.csvgenerator');
          $csv->setName('export_liste-sections');
          $csv->addLine(array('Nom','Num','Délégué','Statut'));

          foreach ($selection as $id) {
            $section = $this->getDoctrine()
              ->getRepository('AppBundle:Section')
              ->find($id);
            $fields = array(
              $section->getNom(),
              $section->getId(),
              ($section->getDelegue()?$section->getDelegue()->getNom().' '.$section->getDelegue()->getPrenom():''),
              ($section->getIsActive()?'Actif':'Fermé'),
            );
            $csv->addLine($fields);
          }
          $csv->generateContent('exports/last-'.$this->getUser()->getId().'.csv');
          
          $path = $this->generateUrl('download_last_export',['fileName'=>'export_liste-sections','type'=>'csv']);
          break;
        default:
          
          break;
      }

      return new Response($path);
    }

    /**
     * @Route("/section/{idSection}/suppression", name="delete_section")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteSectionAction($idSection)
    {
        $section = $this->getDoctrine()
            ->getRepository('AppBundle:Section')
            ->find($idSection);

        $section->setIsActive(false);

        $em = $this->get('doctrine.orm.entity_manager');
        $em->persist($section);
        $em->flush();

        $history = $this->get('app.history');
        $history->init($this->getUser(),['id'=>$idSection,'name'=>'Section'],'DELETE')
                ->log(true); 

        $this->get('session')->getFlashBag()->add('success', 'Suppression effectuée !');

        return $this->redirectToRoute('list_sections');
    }

    /**
     * @Route("/section/{idSection}/delegue/edition", name="section_edit_delegue")
     * @Security("has_role('ROLE_USER')")
     */
    public function editDelegueAction(request $request, $idSection)
    {
        $section = $this->getDoctrine()
            ->getRepository('AppBundle:Section')
            ->find($idSection);

        $idContact = $request->request->get('idDelegue');

        if($idContact){
            $contact = $this->getDoctrine()
                ->getRepository('AppBundle:Contact')
                ->find($idContact);

            $section->setDelegue($contact);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($section);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');

        }        

        return $this->redirectToRoute('view_section',array('idSection'=>$section->getId()));
    }


    /**
     * @Route("/section/{idSection}/patrimoine/enregistrer", name="save_patrimoine")
     * @Security("has_role('ROLE_USER')")
     */
    public function savePatrimoineAction(Request $request, $idSection)
    {

        $section = $this->getDoctrine()
                ->getRepository('AppBundle:Section')
                ->find($idSection);

        if($request->query->get('idPatrimoine')){
            $patrimoine = $this->getDoctrine()
                ->getRepository('AppBundle:Patrimoine')
                ->find($request->query->get('idPatrimoine'));
        }else{
            $patrimoine = new Patrimoine();
            $patrimoine->setSection($section);
        }

        $patrimoineForm = $this->createForm(PatrimoineType::class, $patrimoine);
        $patrimoineForm->handleRequest($request);

        if($patrimoineForm->isSubmitted() && $patrimoineForm->isValid()){

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($patrimoine);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
        }
        if ($patrimoineForm->isSubmitted() && !$patrimoineForm->isValid()) {
          $this->get('app.tools')->handleFormErrors($patrimoineForm);
        }

        return $this->redirectToRoute('view_section',array('idSection'=>$section->getId()));
    }


    /**
     * @Route("/section/{idSection}/destinataires-rentiers/save", name="save_dest_rentiers")
     * @Security("has_role('ROLE_USER')")
     */
    public function saveDestRentiersAction(Request $request, $idSection)
    {

        $section = $this->getDoctrine()
                ->getRepository('AppBundle:Section')
                ->find($idSection);

        //edit nb rentiers
        $ids = $request->request->get('idRentier');

        if($ids){
            foreach ($ids as $id) {

                $contact = $this->getDoctrine()
                    ->getRepository('AppBundle:Contact')
                    ->find($id);

                $contact->setNbRentiers($request->request->get('nbRentier-'.$id));

                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($contact);
                $em->flush();

                
            }
            $this->get('session')->getFlashBag()->add('success', 'Enregistrement(s) effectué(s) !');
        }

        //add if new
        if($request->request->get('selNewRentier')!=0){

            $contact = $this->getDoctrine()
                    ->getRepository('AppBundle:Contact')
                    ->find($request->request->get('selNewRentier'));

            $contact->setIsRentier(true);
            $contact->setNbRentiers($request->request->get('numNewRentier'));

            $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($contact);
                $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Enregistrement effectué !');
        }

        //delete
        $removalIds = $request->request->get('idRemoval');

        if ($removalIds) {
            foreach ($removalIds as $id) {

                $contact = $this->getDoctrine()
                    ->getRepository('AppBundle:Contact')
                    ->find($id);

                $contact->setNbRentiers(null);
                $contact->setIsRentier(false);

                $em = $this->get('doctrine.orm.entity_manager');
                $em->persist($contact);
                $em->flush();

                
            }
            $this->get('session')->getFlashBag()->add('success', 'Suppression(s) effectuée(s) !');
        }


        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function displayFonctionsAdherentsSectionAction($section)
    {
        $contacts = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->findFonctionsSection($section);

        return new Response($this->render('modals/fonctions-adherents.html.twig', [
            'section' => $section,
            'contacts' => $contacts
        ])->getContent());

    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function displayDestinataireRentiersAction($section)
    {
        $contacts = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->findBy(array('section'=>$section,'isRentier'=>true));

        $contactsSection = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->findBy(array('section'=>$section));

        $purgedArray = array();
        foreach ($contactsSection as $contactSection) {
            if($contactSection->getStatutJuridique()&&$contactSection->getStatutJuridique()->getLabel()=='Adhérent'){
                $found = false;
                foreach ($contacts as $contact) {
                    if($contact->getId()==$contactSection->getId()){
                        $found = true;
                    }
                }
                if(!$found){
                    $purgedArray[] = $contactSection;
                }
            }
        }

        return new Response($this->render('modals/destinataires-rentier.html.twig', [
            'section' => $section,
            'contacts' => $contacts,
            'contactsSection' => $purgedArray
        ])->getContent());

    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function displayNbAdherentsAction($section)
    {
        $nbContacts = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->countContactsBySection($section);

        // dump($nbContacts);

        return new Response($nbContacts);

    }

}