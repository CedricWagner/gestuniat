<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Suivi;
use AppBundle\Form\SuiviDefaultType;

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

    	// Section
      	$section = $this->getDoctrine()
          ->getRepository('AppBundle:Section')
          ->find($idSection);

        // Suivis
		$suivi = new Suivi();

		$suiviForm = $this->createForm(SuiviDefaultType::class, $suivi);
		$suiviForm->handleRequest($request);

		if ($suiviForm->isSubmitted() && $suiviForm->isValid()) {
			        
			$datetime = new \DateTime();

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


		return $this->render('operateur/sections/view-section.html.twig', [
		    'section' => $section,
		    'suiviForm' => $suiviForm->createView(),
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
    	
    }

    /**
     * @Route("/section/{idSection}/suppression", name="delete_section")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteSectionAction($idSection)
    {

    }
}