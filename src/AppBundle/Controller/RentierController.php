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
use AppBundle\Entity\DestRentierEnvoi;


class RentierController extends Controller
{

    /**
     * @Route("/rentier/liste/destinataires/individuels/{annee}/{numTrimestre}", name="list_dest_indivs", defaults={"annee" = 0,"numTrimestre" = 0})
     * @Security("has_role('ROLE_SPECTATOR')")
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

        $lstDeces = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->findDeces($annee,$numTrimestre);

        return $this->render('operateur/rentiers/deces.html.twig',[
                'lstDeces'=>$lstDeces,
                'annee'=>$annee,
                'numTrimestre'=>$numTrimestre,
            ]);

    }

    /**
     * @Route("/rentier/liste/destinataires/rentiers/{annee}/{numTrimestre}", name="list_dest_rentiers", defaults={"annee" = 0,"numTrimestre" = 0})
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function listDestRentiersAction($annee,$numTrimestre)
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
            $envoiRentier->setEnvoisRentiers(
                $this->getDoctrine()
                    ->getRepository('AppBundle:DestRentierEnvoi')
                    ->findBy(array('envoiRentier'=>$envoiRentier))
            );
        }

        return $this->render('operateur/rentiers/envois-rentiers.html.twig',[
                'envoisRentiers'=>$envoisRentiers,
                'annee'=>$annee,
                'numTrimestre'=>$numTrimestre,
            ]);

    }

    /**
     * @Route("/rentier/liste/deces-rentiers/{annee}/{numTrimestre}", name="list_deces_rentiers", defaults={"annee" = 0,"numTrimestre" = 0})
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function listDecesRentiersAction($annee,$numTrimestre)
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

        $lstDeces = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->findDeces($annee,$numTrimestre);

        return $this->render('operateur/rentiers/deces.html.twig',[
                'lstDeces'=>$lstDeces,
                'annee'=>$annee,
                'numTrimestre'=>$numTrimestre,
            ]);

    }

    /**
     * @Route("/rentier/liste/donateurs/{annee}/{numTrimestre}", name="list_donateurs_rentiers", defaults={"annee" = 0,"numTrimestre" = 0})
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function listDonateursRentiersAction($annee,$numTrimestre)
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

        $lstDonateurs = $this->getDoctrine()
            ->getRepository('AppBundle:Don')
            ->findDons($annee,$numTrimestre);

        return $this->render('operateur/rentiers/donateurs.html.twig',[
                'lstDonateurs'=>$lstDonateurs,
                'annee'=>$annee,
                'numTrimestre'=>$numTrimestre,
            ]);

    }

    /**
     * @Route("/rentier/liste/ags/{annee}/{numTrimestre}", name="list_ags_rentiers", defaults={"annee" = 0,"numTrimestre" = 0})
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function listAGsRentiersAction($annee,$numTrimestre)
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

    	$lstAGs = $this->getDoctrine()
    		->getRepository('AppBundle:AssembleeGenerale')
    		->findAGs($annee,$numTrimestre);

    	return $this->render('operateur/rentiers/ags.html.twig',[
    			'lstAGs'=>$lstAGs,
    			'annee'=>$annee,
    			'numTrimestre'=>$numTrimestre,
    		]);

    }

    /**
     * @Route("/rentier/generer-factures/destinataires/individuels/{annee}/{numTrimestre}", name="generate_factures_envois_indiv", defaults={"annee" = 0,"numTrimestre" = 0})
     * @Security("has_role('ROLE_SPECTATOR')")
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
     * @Route("/rentier/export/destinataires/individuels/{annee}/{numTrimestre}.{format}", name="export_liste_dest_indiv", defaults={"annee" = 0,"numTrimestre" = 0}, requirements={"format":"pdf|csv"})
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function exportListeDestIndivsAction(Request $request,$annee,$numTrimestre,$format)
    {

        $datetime = new \DateTime();

        $envoisRentiers = $this->getDoctrine()
            ->getRepository('AppBundle:EnvoiRentier')
            ->findBy(array('annee'=>$annee,'numTrimestre'=>$numTrimestre));


        $lstDests = array();

        foreach ($envoisRentiers as $envoiRentier) {
            $destIndivEnvois = $this->getDoctrine()
                ->getRepository('AppBundle:DestIndivEnvoi')
                ->findBy(array('envoiRentier'=>$envoiRentier));

            $envoiRentier->setEnvoisIndiv($destIndivEnvois);
            $lstDests = array_merge($lstDests,$destIndivEnvois);
        }

        switch ($format) {
            case 'csv':
                
                $csv = $this->get('app.csvgenerator');
                $csv->setName('export_liste-dest-indiv');
                

                $csv->addLine(array('Nom','Prénom','Adresse','Code postal','Commune','Pays','Boite postale'));

                foreach ($lstDests as $dest) {
                    $fields = array($dest->getContact()->getNom(),$dest->getContact()->getPrenom(),$dest->getContact()->getAdresse().' '.$dest->getContact()->getAdresseComp(),$dest->getContact()->getCp(),$dest->getContact()->getCommune(),$dest->getContact()->getPays(),$dest->getContact()->getBp());
                    
                    $csv->addLine($fields);
                }

                return new Response($csv->generateContent(),200,$csv->getHeaders());
                break;
            default:
                return $this->redirectToRoute('list_dest_indivs',array(
                        'annee'=>$annee,
                        'numTrimestre'=>$numTrimestre,
                    ));
                break;
        }

    }

    /**
     * @Route("/rentier/export/deces/{annee}/{numTrimestre}.{format}", name="export_liste_deces", defaults={"annee" = 0,"numTrimestre" = 0}, requirements={"format":"pdf|csv"})
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function exportListeDecesAction(Request $request,$annee,$numTrimestre,$format)
    {

        $datetime = new \DateTime();

        $lstDeces = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->findDeces($annee,$numTrimestre);

        switch ($format) {
            case 'csv':
                
                $csv = $this->get('app.csvgenerator');
                $csv->setName('export_liste-deces');
                

                $csv->addLine(array('Num Adh','Nom','Prénom','Commune','Code postale','Section','Date de décès'));

                foreach ($lstDeces as $deces) {
                    $fields = array($deces->getNumAdh(),$deces->getNom(),$deces->getPrenom(),$deces->getCommune(),$deces->getCp(),$deces->getSection()?$deces->getSection()->getNom():'aucune section',$deces->getDateDeces()->format('d/m/Y'));
                    
                    $csv->addLine($fields);
                }

                return new Response($csv->generateContent(),200,$csv->getHeaders());
                break;
            default:
                return $this->redirectToRoute('list_deces_rentiers',array(
                        'annee'=>$annee,
                        'numTrimestre'=>$numTrimestre,
                    ));
                break;
        }

    }

    /**
     * @Route("/rentier/export/donateurs/{annee}/{numTrimestre}.{format}", name="export_liste_donateurs", defaults={"annee" = 0,"numTrimestre" = 0}, requirements={"format":"pdf|csv"})
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function exportListeDonateursAction(Request $request,$annee,$numTrimestre,$format)
    {

        $datetime = new \DateTime();

        $lstDons = $this->getDoctrine()
            ->getRepository('AppBundle:Don')
            ->findDons($annee,$numTrimestre);

        switch ($format) {
            case 'csv':
                
                $csv = $this->get('app.csvgenerator');
                $csv->setName('export_liste-donateurs');
                
                $csv->addLine(array('Num Adh','Nom','Prénom','Commune','Code postale','Section','Montant','Date de décès'));

                foreach ($lstDons as $don) {
                    $fields = array($don->getContact()->getNumAdh(),$don->getContact()->getNom(),$don->getContact()->getPrenom(),$don->getContact()->getCommune(),$don->getContact()->getCp(),$don->getContact()->getSection()?$don->getContact()->getSection()->getNom():'aucune section',$don->getMontant(),$don->getDate()->format('d/m/Y'));
                    
                    $csv->addLine($fields);
                }

                return new Response($csv->generateContent(),200,$csv->getHeaders());
                break;
            default:
                return $this->redirectToRoute('list_donateurs_rentiers',array(
                        'annee'=>$annee,
                        'numTrimestre'=>$numTrimestre,
                    ));
                break;
        }
    }

    /**
     * @Route("/rentier/export/destinataires/rentiers/{annee}/{numTrimestre}.{format}", name="export_liste_dest_rentiers", defaults={"annee" = 0,"numTrimestre" = 0}, requirements={"format":"pdf|csv"})
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function exportListeDestRentiersAction(Request $request,$annee,$numTrimestre,$format)
    {

        $datetime = new \DateTime();

        $envoisRentiers = $this->getDoctrine()
            ->getRepository('AppBundle:EnvoiRentier')
            ->findBy(array('annee'=>$annee,'numTrimestre'=>$numTrimestre));


        $lstDests = array();

        foreach ($envoisRentiers as $envoiRentier) {
            $destRentierEnvois = $this->getDoctrine()
                ->getRepository('AppBundle:DestRentierEnvoi')
                ->findBy(array('envoiRentier'=>$envoiRentier));

            $envoiRentier->setEnvoisRentiers($destRentierEnvois);
            $lstDests = array_merge($lstDests,$destRentierEnvois);
        }

        switch ($format) {
            case 'csv':
                
                $csv = $this->get('app.csvgenerator');
                $csv->setName('export_liste-depositaires-rentier');
                
                $csv->addLine(array('Nom','Prénom','Nombre','Adresse','Code postal','Commune','Pays','Boite postale'));

                foreach ($lstDests as $dest) {
                    if($dest->getNb()>50){
                        $nb = $dest->getNb();
                        while ($nb > 0) {
                            $fields = array($dest->getContact()->getNom(),$dest->getContact()->getPrenom(),($nb>50?50:$nb),$dest->getContact()->getAdresse().' '.$dest->getContact()->getAdresseComp(),$dest->getContact()->getCp(),$dest->getContact()->getCommune(),$dest->getContact()->getPays(),$dest->getContact()->getBp());
                            
                            $csv->addLine($fields);
                            $nb = $nb - 50;
                        }
                    }else{
                        $fields = array($dest->getContact()->getNom(),$dest->getContact()->getPrenom(),$dest->getNb(),$dest->getContact()->getAdresse().' '.$dest->getContact()->getAdresseComp(),$dest->getContact()->getCp(),$dest->getContact()->getCommune(),$dest->getContact()->getPays(),$dest->getContact()->getBp());
                        $csv->addLine($fields);
                    }
                    
                }

                return new Response($csv->generateContent(),200,$csv->getHeaders());
                break;
            default:
                return $this->redirectToRoute('list_dest_indivs',array(
                        'annee'=>$annee,
                        'numTrimestre'=>$numTrimestre,
                    ));
                break;
        }

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
                //for prod
				// $numTrimestre = false;
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

		        $contacts = $this->getDoctrine()
		        	->getRepository('AppBundle:Contact')
		        	->findBy(array('isOffreDecouverte'=>true,'section'=>$section));

                foreach ($contacts as $contact) {
                    $destIndivEnvoi = new DestIndivEnvoi();
                    $destIndivEnvoi->setContact($contact);
                    $destIndivEnvoi->setEnvoiRentier($envoiRentier);

                    $em = $this->get('doctrine.orm.entity_manager');
                    $em->persist($destIndivEnvoi);
                    $em->flush();
                }

                $contacts = $this->getDoctrine()
                    ->getRepository('AppBundle:Contact')
                    ->findBy(array('isRentier'=>true,'section'=>$section));

                foreach ($contacts as $contact) {
                    $destRentierEnvoi = new DestRentierEnvoi();
                    $destRentierEnvoi->setContact($contact);
                    $destRentierEnvoi->setEnvoiRentier($envoiRentier);
                    $destRentierEnvoi->setNb($contact->getNbRentiers());

                    $em = $this->get('doctrine.orm.entity_manager');
                    $em->persist($destRentierEnvoi);
                    $em->flush();
                }
			}
		}

		return new Response('',Response::HTTP_OK);

	}

}