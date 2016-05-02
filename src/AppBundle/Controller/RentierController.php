<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\EnvoiRentier;
use AppBundle\Entity\DestIndivEnvoi;


class RentierController extends Controller
{

    /**
     * @Route("/rentier/liste/destinataires/individuels/{annee}/{numTrimestre}", name="list_dest_indivs", defaults={"annee" = 0,"numTrimestre" = 0})
     * @Security("has_role('ROLE_USER')")
     */
    public function listDestIndivsAction($annee,$numTrimestre)
    {
    	$datetime = new \DateTime();
    	if ($annee==0) {
    		$annee = $this->getDoctrine()
    			->getRepository('AppBundle:EnvoiRentier')
    			->findLastAnnee();
    	}
    	if ($numTrimestre==0) {
    		$numTrimestre = $this->getDoctrine()
    			->getRepository('AppBundle:EnvoiRentier')
    			->findLastTrimestre($annee);
    	}

    	$envoisRentiers = $this->getDoctrine()
    		->getRepository('AppBundle:EnvoiRentier')
    		->findBy(array('annee'=>$annee,'numTrimestre'=>$numTrimestre),array('section'=>'ASC'));

    	foreach ($envoisRentiers as $envoiRentier) {
    		$envoiRentier->setEnvoisIndiv(
				$this->getDoctrine()
					->getRepository('AppBundle:DestIndivEnvoi')
					->findBy(array('envoiRentier'=>$envoiRentier))
			);
    	}

    	return $this->render('operateur/rentiers/envois-indiv.html.twig',[
    			'envoisRentiers'=>$envoisRentiers,
    			'annee'=>$annee,
    			'numTrimestre'=>$numTrimestre,
    		]);

    }

    /**
     * @Route("/rentier/generer-factures/destinataires/individuels/{annee}/{numTrimestre}", name="generate_factures_envois_indiv", defaults={"annee" = 0,"numTrimestre" = 0})
     * @Security("has_role('ROLE_USER')")
     */
    public function generateFactureDestIndivsAction(Request $request,$annee,$numTrimestre)
    {

    	$datetime = new \DateTime();

    	$cout =  $request->request->get('txtCoutUnitaire');

    	$envoisRentiers = $this->getDoctrine()
    		->getRepository('AppBundle:EnvoiRentier')
    		->findBy(array('annee'=>$annee,'numTrimestre'=>$numTrimestre));

    	foreach ($envoisRentiers as $envoiRentier) {
    		$envoiRentier->setDateGenFacture($datetime);
    		$envoiRentier->setCoutEnvoisIndiv($cout);

			$em = $this->get('doctrine.orm.entity_manager');
			$em->persist($envoiRentier);
			$em->flush();
    	}

    	return $this->redirectToRoute('list_dest_indivs',['annee'=>$annee,'numTrimestre'=>$numTrimestre]);

    }

    /**
     * @Route("/rentier/liste/{idFilter}/{page}/{nb}", name="list_rentiers", defaults={"idFilter" = 0,"page" = 1,"nb" = 0})
     * @Security("has_role('ROLE_USER')")
     */
    public function listRentiersAction($idFilter,$page,$nb)
    {
    	$currentFilter = null;

        if ($idFilter!=0) {
            $currentFilter = $this->getDoctrine()
              ->getRepository('AppBundle:FiltrePerso')
              ->find($idFilter);

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
			$rentiers = $this->getDoctrine()
			  ->getRepository('AppBundle:Section')
			  ->findByFilter($filtreValeurs,$page,$nb);
        }else{
			$rentiers = $this->getDoctrine()
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
	* @Route("/rentier/generer/destinataires/individuels", name="generate_dest_indiv")
	*/
	public function generateDestIndivAction()
	{

		$datetime = new \DateTime();

		switch ($datetime->format('d/m')) {
			case '05/01':
				$numTrimestre = 1;
				break;
			case '05/04':
				$numTrimestre = 2;
				break;
			case '05/07':
				$numTrimestre = 3;
				break;
			case '05/10':
				$numTrimestre = 4;
				break;
			default:
				//for test only
				$numTrimestre = 2;
				break;
		}

		if($numTrimestre){

			$sections = $this->getDoctrine()
				->getRepository('AppBundle:Section')
				->findBy(array('isActive'=>true));

			foreach ($sections as $section) {

				$envoiRentier = new EnvoiRentier();
				$envoiRentier->setDate($datetime);
				$envoiRentier->setAnnee($datetime->format('Y'));
				$envoiRentier->setNumTrimestre($numTrimestre);
				$envoiRentier->setSection($section);

		        $em = $this->get('doctrine.orm.entity_manager');
		        $em->persist($envoiRentier);
		        $em->flush();

		        $contacts = $this->getDoctrine()
		        	->getRepository('AppBundle:Contact')
		        	->findBy(array('isEnvoiIndiv'=>true,'section'=>$section));

		        foreach ($contacts as $contact) {
					$destIndivEnvoi = new DestIndivEnvoi();
					$destIndivEnvoi->setContact($contact);
					$destIndivEnvoi->setEnvoiRentier($envoiRentier);

			        $em = $this->get('doctrine.orm.entity_manager');
			        $em->persist($destIndivEnvoi);
			        $em->flush();
		        }
			}
		}

		return new Response('',Response::HTTP_OK);

	}

}