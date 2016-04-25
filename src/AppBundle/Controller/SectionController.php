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
use AppBundle\Form\SuiviDefaultType;
use AppBundle\Form\PatrimoineType;
use AppBundle\Form\SectionFullType;

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
            'sections' => $sections,
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

			return  $this->redirectToRoute('view_section', array('idSection' => $section->getId()));
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


		return $this->render('operateur/sections/view-section.html.twig', [
            'section' => $section,
            'displayedYears' => $displayedYears,
            'allYears' => $allYears,
            'patrimoines' => $patrimoines,
		    'patrimoineForms' => $patrimoineForms,
            'suiviForm' => $suiviForm->createView(),
		    'newPatrimoineForm' => $newPatrimoineForm->createView(),
		    'lstSuivis' => $lstSuivis,
		    'lstAllSuivis' => $lstAllSuivis,
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
          }

        return $this->render('operateur/sections/full-section.html.twig', [
            'section' => $section,
            'isInsert' => false,
            'sectionForm' => $sectionForm->createView(),
        ]);

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

            return $this->redirectToRoute('view_section',array('idSection'=>$section->getId()));  
        }

        return $this->render('operateur/sections/full-section.html.twig', [
            'section' => $section,
            'isInsert' => true,
            'sectionForm' => $sectionForm->createView(),
        ]);


    }

    /**
     * @Route("/section/{idSection}/suppression", name="delete_section")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteSectionAction($idSection)
    {

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
        }

        return $this->redirectToRoute('view_section',array('idSection'=>$section->getId()));
    }
}