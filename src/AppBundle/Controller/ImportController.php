<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Civilite;
use AppBundle\Entity\Diplome;
use AppBundle\Entity\AssocImport;
use AppBundle\Entity\StatutMatrimonial;
use AppBundle\Entity\MoyenPaiement;
use AppBundle\Entity\Periodicite;
use AppBundle\Entity\RegimeAffiliation;
use AppBundle\Entity\StatutJuridique;
use AppBundle\Entity\TypeTournee;
use AppBundle\Entity\FonctionSection;
use AppBundle\Entity\FonctionGroupement;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Section;
use AppBundle\Entity\AssembleeGenerale;
use AppBundle\Entity\Permanence;
use AppBundle\Entity\Patrimoine;
use AppBundle\Entity\Effectif;
use AppBundle\Entity\RemiseTimbre;
use AppBundle\Entity\Organisme;
use AppBundle\Entity\TypeOrganisme;
use AppBundle\Entity\Don;
use AppBundle\Entity\Procuration;
use AppBundle\Entity\Vignette;
use AppBundle\Entity\Pouvoir;
use AppBundle\Entity\ContratPrevObs;
use AppBundle\Entity\ContratPrevoyance;
use AppBundle\Entity\Cotisation;
use AppBundle\Entity\Suivi;
use AppBundle\Entity\ContactDiplome;



class ImportController extends Controller
{

	private $files = array(
		'adherents'=>'gun_t_Adh.txt',
		'etats'=>'gun_t_AdhEtat.txt',
		'suivis'=>'gun_t_AdhSuivi.txt',
		'ags'=>'gun_t_AG.txt',
		'ayantsDroits'=>'gun_t_AyantsDroit.txt',
		'cotisations'=>'gun_t_Cotisations.txt',
		'cotisations2'=>'gun_t_Cotisations_a.txt',
		'diplomes'=>'gun_t_Diplome.txt',
		'dons'=>'gun_t_Dons.txt',
		'effectifs'=>'gun_t_Effectif.txt',
		'membres'=>'gun_t_Membre.txt',
		'obseques'=>'gun_t_Obseque.txt',
		'organismes'=>'gun_t_Organisme.txt',
		'patrimoines'=>'gun_t_Patrimoine.txt',
		'pouvoirs'=>'gun_t_Pouvoir.txt',
		'prevoyances'=>'gun_t_Prev.txt',
		'procurations'=>'gun_t_Procuration.txt',
		'sections'=>'gun_t_Section.txt',
		'timbres'=>'gun_t_Timbres.txt',
		'tournees'=>'gun_t_Tournee.txt',
		'vignettes'=>'gun_t_Vignette.txt',
	);

	private $systemFiles = array(
		'civilites'=>'civ.csv',
		'diplomes'=>'diplomes.csv',
		'statutsMatrimoniaux'=>'matrim.csv',
		'moyPaiements'=>'moy_paiement.csv',
		'periodicites'=>'periodicites.csv',
		'regimesAffiliations'=>'regimes_affiliations.csv',
		'statutsJuridiques'=>'statuts_juridiques.csv',
		'typesTournees'=>'types_tournees.csv',
		'fonctionsSection'=>'fonctions_section.csv',
		'fonctionsGroupement'=>'fonctions_groupement.csv',
		'typesOrganisme'=>'types_organismes.csv',
	);

	private $count = 0;
	private $state = 'pending';


	/**
	* @Route("/import-vue", name="import_view")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function importViewAction()
	{
		return $this->render('admin/import.html.twig');
	}

	/**
	* @Route("/import/{path}/{mode}", name="import")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function importAction($path,$mode)
	{
		try{
			foreach ($this->files as $key => $value) {
				if (!file_exists('import/'.$path.'/'.$value)) {
					throw new \Exception("Fichier introuvable : ".$value, 1);
				}
			}

			foreach ($this->systemFiles as $key => $value) {
				if (!file_exists('import/system/'.$value)) {
					throw new \Exception("Fichier système introuvable : ".$value, 1);
				}
			}
	
			switch ($mode) {
				case 'full':
					$this->getClearTablesAction();
					dump('=========== cleared');
					$this->importSystem();
					dump('=========== system imported');
					$this->importFiles($path);
					dump('=========== files imported');
					break;
				case 'delete':
					$this->getClearTablesAction();
					dump('=========== cleared');
					break;
				case 'filesOnly':
					$this->importFiles($path);
					dump('=========== files imported');
					break;
				case 'systemOnly':
					$this->importSystem();
					dump('=========== system imported');
					break;
			}
		}catch(\Exception $e){
			$this->state = $e->getMessage();
		}

		return new Response($this->state);
	}

	/**
	* @Route("/import-getClear", name="get_clear")
	* @Security("has_role('ROLE_ADMIN')")
	*/
	public function getClearTablesAction(){
		$em = $this->get('doctrine.orm.entity_manager');

		$toClear = array(
				'alerte',
				'assemblee_generale',
				'assoc_import',
				'civilite',
				'contact',
				'contact_diplome',
				'contrat_prev_obs',
				'cotisation',
				'dest_indiv_envoi',
				'dest_rentier_envoi',
				'diplome',
				'document',
				'document_section',
				'don',
				'dossier',
				'effectif',
				'envoi_rentier',
				'fonction_groupement',
				'fonction_section',
				'moyen_paiement',
				'offre_decouverte_envoi',
				'organisme',
				'organisme_envoi',
				'patrimoine',
				'periodicite',
				'permanence',
				'pouvoir',
				'procuration',
				'regime_affiliation',
				'remise_timbre',
				'section',
				'statut_juridique',
				'statut_matrimonial',
				'suivi',
				'type_organisme',
				'type_tournee',
				'vignette',
			);

		$txt = '';
		foreach ($toClear as $table) {
			$em->getConnection()->query('DELETE FROM '.$table.' WHERE 1;');
			// $em->getConnection()->query('START TRANSACTION;SET FOREIGN_KEY_CHECKS=0; TRUNCATE '.$table.'; SET FOREIGN_KEY_CHECKS=1; COMMIT;');
			$txt .= 'DELETE FROM '.$table.' WHERE 1;';
		}

		return new Response($txt);

	}

	private function importSystem(){
		$rootSystem = 'import/system/';

		$em = $this->get('doctrine.orm.entity_manager');

		// CIVILITES
		$handle = fopen($rootSystem.$this->systemFiles['civilites'], 'r');
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        if($data[1] != ''){
		        $civilite = new Civilite();
		        $civilite->setLabel($data[1]);
	        
				$em->persist($civilite);
				$em->flush();

				$this->createAssoc($em,$data[0],$civilite->getId(),'civilite');
	        }

	    }
	    dump('==== civilites imported');

		// DIPLOMES
		$handle = fopen($rootSystem.$this->systemFiles['diplomes'], 'r');
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        if($data[1] != ''){
		        $diplome = new Diplome();
		        $diplome->setLabel(utf8_encode($data[1]));
	        
				$em->persist($diplome);
				$em->flush();

				$this->createAssoc($em,$data[0],$diplome->getId(),'diplome');
	        }
	    }
	    dump('==== diplomes imported');

		// STATUTS MATR
		$handle = fopen($rootSystem.$this->systemFiles['statutsMatrimoniaux'], 'r');
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        if($data[1] != ''){
		        $statutMatrimonial = new StatutMatrimonial();
		        $statutMatrimonial->setLabel(utf8_encode($data[1]));
	        
				$em->persist($statutMatrimonial);
				$em->flush();

				$this->createAssoc($em,$data[0],$statutMatrimonial->getId(),'statutMatrimonial');
	        }
	    }
	    dump('==== statuts matrimoniaux imported');


		// MOYEN PAIEMENTS
		$handle = fopen($rootSystem.$this->systemFiles['moyPaiements'], 'r');
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        if($data[1] != ''){
		        $moyenPaiement = new MoyenPaiement();
		        $moyenPaiement->setLabel(utf8_encode($data[1]));
	        
				$em->persist($moyenPaiement);
				$em->flush();

				$this->createAssoc($em,$data[0],$moyenPaiement->getId(),'moyenPaiement');
	        }
	    }
	    dump('==== moyen paiements imported');


		// PERIODICITE
		$handle = fopen($rootSystem.$this->systemFiles['periodicites'], 'r');
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        if($data[1] != ''){
		        $periodicite = new Periodicite();
		        $periodicite->setLabel(utf8_encode($data[1]));
	        
				$em->persist($periodicite);
				$em->flush();

				$this->createAssoc($em,$data[0],$periodicite->getId(),'periodicite');
	        }
	    }
	    dump('==== periodicites imported');

		// REGIMES AFFILILIATION
		$handle = fopen($rootSystem.$this->systemFiles['regimesAffiliations'], 'r');
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        if($data[1] != ''){
		        $regimeAffiliation = new RegimeAffiliation();
		        $regimeAffiliation->setLabel(utf8_encode($data[1]));
	        
				$em->persist($regimeAffiliation);
				$em->flush();

				$this->createAssoc($em,$data[0],$regimeAffiliation->getId(),'regimeAffiliation');
	        }
	    }
	    dump('==== regime aff imported');

		// STATUT JURIDIQUE
		$handle = fopen($rootSystem.$this->systemFiles['statutsJuridiques'], 'r');
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        if($data[1] != ''){
		        $statutJuridique = new StatutJuridique();
		        $statutJuridique->setLabel(utf8_encode($data[1]));
	        
				$em->persist($statutJuridique);
				$em->flush();

				$this->createAssoc($em,$data[0],$statutJuridique->getId(),'statutJuridique');
	        }
	    }
	    $statutJuridique = new StatutJuridique();
        $statutJuridique->setLabel('Prospect');

		$em->persist($statutJuridique);

	    $statutJuridique = new StatutJuridique();
        $statutJuridique->setLabel('Poursuite d\'adhésion');

		$em->persist($statutJuridique);
		$em->flush();	

	    dump('==== statutJuridique imported');

		// TYPE TOURNEE
		$handle = fopen($rootSystem.$this->systemFiles['typesTournees'], 'r');
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        if($data[1] != ''){
		        $typeTournee = new TypeTournee();
		        $typeTournee->setLabel(utf8_encode($data[1]));
	        
				$em->persist($typeTournee);
				$em->flush();

				$this->createAssoc($em,$data[0],$typeTournee->getId(),'typeTournee');
	        }
	    }
	    dump('==== typeTournee imported');

	    
		// FONCTION SECTION
		$handle = fopen($rootSystem.$this->systemFiles['fonctionsSection'], 'r');
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        if($data[1] != ''){
		        $fonctionSection = new FonctionSection();
		        $fonctionSection->setLabel(utf8_encode($data[1]));
	        
				$em->persist($fonctionSection);
				$em->flush();

				$this->createAssoc($em,$data[0],$fonctionSection->getId(),'fonctionSection');
	        }
	    }
	    dump('==== fonctionSection imported');


		// FONCTION GROUPEMENT
		$handle = fopen($rootSystem.$this->systemFiles['fonctionsGroupement'], 'r');
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        if($data[1] != ''){
		        $fonctionGroupement = new FonctionGroupement();
		        $fonctionGroupement->setLabel(utf8_encode($data[1]));
	        
				$em->persist($fonctionGroupement);
				$em->flush();

				$this->createAssoc($em,$data[0],$fonctionGroupement->getId(),'fonctionGroupement');
	        }
	    }
	    dump('==== fonctionGroupement imported');

		// TYPE ORGANISME
		$handle = fopen($rootSystem.$this->systemFiles['typesOrganisme'], 'r');
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        if($data[1] != ''){
		        $typeOrganisme = new TypeOrganisme();
		        $typeOrganisme->setLabel(utf8_encode($data[1]));
	        
				$em->persist($typeOrganisme);
				$em->flush();

				$this->createAssoc($em,$data[0],$typeOrganisme->getId(),'typeOrganisme');
	        }
	    }
	    dump('==== typeOrganisme imported');


	    fclose($handle);

	    $em->flush();

	}

	public function importFiles($path){
		$em = $this->get('doctrine.orm.entity_manager');

		try {
			
			// $this->importSection($path,$em);
			// $this->importAG($path,$em);
			// $this->importPermanence($path,$em);
			// $this->importPatrimoine($path,$em);
			// $this->importEffectif($path,$em);
			// $this->importRemiseTimbre($path,$em);
			// $this->importOrganisme($path,$em);
			// $this->importContact($path,$em);
			// $this->importDon($path,$em);
			// $this->importProcuration($path,$em);
			// $this->importVignette($path,$em);
			// $this->importPouvoir($path,$em);
			// $this->importContratPrevObs($path,$em);
			// $this->importContratPrevoyance($path,$em);
			// $this->importSuivi($path,$em);
			// $this->importDiplome($path,$em);
			// $this->importCotisation($path,$em);

			$em->flush();
			
			$this->importFix($path,$em);
			
			// $em->flush();

			$this->state = 'ok';
		} catch (\LengthException $e) {
			$em->flush();
		}

	}

	public function importFix($path,$em){

  //   	$maxId = $this->getDoctrine()
		//  ->getRepository('AppBundle:AssocImport')
		//  ->findMaxId('contact-fix-1');
		// $handle = fopen('import/'.$path.'/'.$this->files['membres'], 'r');
	 //    $row = 0;
	 //    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 400) {
	 //        if(intval($data[0]) && $data[2]=="True" && $data[3] != "" && intval($data[0])>$maxId){
		//         $this->incrementsCount();
		//         $assocContact = $this->getDoctrine()
		//         	->getRepository('AppBundle:AssocImport')
		//         	->findOneBy(array('oldId'=>$data[0],'entity'=>'contact-membre'));
		//         if($assocContact){
		// 	        $contact = $this->getDoctrine()
		// 	        	->getRepository('AppBundle:Contact')
		// 	        	->find($assocContact->getNewId());
			     	
		// 	        $contact->setDateAdhesion(new \DateTime($data[3]));

		// 	        $em->persist($contact);

		// 	        $this->createAssoc($em,$data[0],$contact->getId(),'contact-fix-1');
		//         }
	 //        }
	 //    }


    	$maxId = $this->getDoctrine()
		 ->getRepository('AppBundle:AssocImport')
		 ->findMaxId('contact-fix-2');
		$handle = fopen('import/'.$path.'/'.$this->files['membres'], 'r');
	    $row = 0;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 400) {
	        if(intval($data[0]) && intval($data[0])>$maxId && $data[21] != ""){
		        $this->incrementsCount();
		        $assocContact = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[0],'entity'=>'contact-membre'));
		        if($assocContact){
			        $contact = $this->getDoctrine()
			        	->getRepository('AppBundle:Contact')
			        	->find($assocContact->getNewId());
			     	
			        $contact->setDateSaisieDeces(new \DateTime($data[21]));

			        $em->persist($contact);

			        $this->createAssoc($em,$data[0],$contact->getId(),'contact-fix-2');
		        }
	        }
	    }

    	$maxId = $this->getDoctrine()
		 ->getRepository('AppBundle:AssocImport')
		 ->findMaxId('don-fix');
		$handle = fopen('import/'.$path.'/'.$this->files['dons'], 'r');
	    $row = 0;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 400) {
	        if(intval($data[0]) && intval($data[0])>$maxId){
		        $this->incrementsCount();
		        $assocDon = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[0],'entity'=>'don'));
		        if($assocDon){
			        $don = $this->getDoctrine()
			        	->getRepository('AppBundle:Don')
			        	->find($assocDon->getNewId());
			     	
			        $don->setDateSaisie(new \DateTime($data[9]));

			        $em->persist($don);

			        $this->createAssoc($em,$data[0],$don->getId(),'don-fix');
		        }
	        }
	    }

	}

	public function createAssoc($em,$oldId,$newId,$entity,$meta=null){
        $assoc = new AssocImport();
        $assoc->setOldId($oldId);
        $assoc->setNewId($newId);
        $assoc->setEntity($entity);
        $assoc->setMeta($meta);

		$em->persist($assoc);
		// $em->flush();

        return $assoc;
    }

    public function importSection($path,$em){
		// SECTION
		
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('section');
		$handle = fopen('import/'.$path.'/'.$this->files['sections'], 'r');
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	        if($data[1] != '' && intval($data[0]) > $maxId ){
		        $section = new Section();
		        $section->populateFromCSV($data);

		        if ($section->getNom()) {
					$em->persist($section);
					$em->flush();

					$this->createAssoc($em,$data[0],$section->getId(),'section');
		        }
	        }
	    }
	    dump('==== sections imported');
    }

    public function importAG($path,$em){
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('ag');

		$handle = fopen('import/'.$path.'/'.$this->files['ags'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        dump('Line : '.$data[0].' Count : '.$this->count);
		        set_time_limit(20);
		        $ag = new AssembleeGenerale();
		        $ag->populateFromCSV($data);
		        
		        $assocSection = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'section'));
		        if($assocSection){
			        $section = $this->getDoctrine()
			        	->getRepository('AppBundle:Section')
			        	->find($assocSection->getNewId());
			        $ag->setSection($section);		        	
		        }

				$em->persist($ag);
				$em->flush();

				$this->createAssoc($em,$data[0],$ag->getId(),'ag');
	        }
	    }
	    dump('==== ag imported');
    }

    public function importPermanence($path,$em){
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('permanence');

		$handle = fopen('import/'.$path.'/'.$this->files['tournees'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $perm = new Permanence();
		        $perm->populateFromCSV($data);
		        
		        $assocSection = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'section'));
		        if($assocSection){
			        $section = $this->getDoctrine()
			        	->getRepository('AppBundle:Section')
			        	->find($assocSection->getNewId());
			        $perm->setSection($section);		        	
		        }

		        $assocTypeTournee = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[2],'entity'=>'typeTournee'));
		        if($assocTypeTournee){
			        $typeTournee = $this->getDoctrine()
			        	->getRepository('AppBundle:TypeTournee')
			        	->find($assocTypeTournee->getNewId());
			        $perm->setTypeTournee($typeTournee);		        	
		        }

		        $assocPeriodicite = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[6],'entity'=>'periodicite'));
		        if($assocPeriodicite){
			        $period = $this->getDoctrine()
			        	->getRepository('AppBundle:Periodicite')
			        	->find($assocPeriodicite->getNewId());
			        $perm->setPeriodicite($period);		        	
		        }

				$em->persist($perm);
				$em->flush();

				$this->createAssoc($em,$data[0],$perm->getId(),'permanence');
	        }
	    }
	    dump('==== permanences imported');
    }



    public function importPatrimoine($path,$em){

    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('patrimoine');

		$handle = fopen('import/'.$path.'/'.$this->files['patrimoines'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $patrimoine = new Patrimoine();
		        $patrimoine->populateFromCSV($data);
		        
		        $assocSection = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'section'));
		        if($assocSection){
			        $section = $this->getDoctrine()
			        	->getRepository('AppBundle:Section')
			        	->find($assocSection->getNewId());
			        $patrimoine->setSection($section);		        	
		        }

				$em->persist($patrimoine);
				$em->flush();

				$this->createAssoc($em,$data[0],$patrimoine->getId(),'patrimoine');
	        }
	    }
	    dump('==== patrimoine imported');
    }

    public function importEffectif($path,$em){
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('effectif');

		$handle = fopen('import/'.$path.'/'.$this->files['effectifs'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $effectif = new Effectif();
		        $effectif->populateFromCSV($data);
		        
		        $assocSection = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'section'));
		        if($assocSection){
			        $section = $this->getDoctrine()
			        	->getRepository('AppBundle:Section')
			        	->find($assocSection->getNewId());
			        $effectif->setSection($section);		        	
		        }

				$em->persist($effectif);
				$em->flush();

				$this->createAssoc($em,$data[0],$effectif->getId(),'effectif');
	        }
	    }
	    dump('==== effectif imported');
    }

    public function importRemiseTimbre($path,$em){
		
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('remiseTimbre');

		$handle = fopen('import/'.$path.'/'.$this->files['timbres'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 100) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $remiseTimbre = new RemiseTimbre();
		        $remiseTimbre->populateFromCSV($data);
		        
		        $assocSection = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'section'));
		        if($assocSection){
			        $section = $this->getDoctrine()
			        	->getRepository('AppBundle:Section')
			        	->find($assocSection->getNewId());
			        $remiseTimbre->setSection($section);		        	
		        }

		        // dump($remiseTimbre);

				$em->persist($remiseTimbre);
				$em->flush();

				$this->createAssoc($em,$data[0],$remiseTimbre->getId(),'remiseTimbre');
	        }
	    }
	    dump('==== remiseTimbre imported');
    }

    public function importOrganisme($path,$em){
		
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('organisme');

		$handle = fopen('import/'.$path.'/'.$this->files['organismes'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $organisme = new Organisme();
		        $organisme->populateFromCSV($data);

		        $assocTypeOrg = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'typeOrganisme'));
		        if($assocTypeOrg){
			        $typeOrg = $this->getDoctrine()
			        	->getRepository('AppBundle:TypeOrganisme')
			        	->find($assocTypeOrg->getNewId());
			        $organisme->setTypeOrganisme($typeOrg);		        	
		        }

		        $assocCivilite = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[2],'entity'=>'civilite'));
		        if($assocCivilite){
			        $civilite = $this->getDoctrine()
			        	->getRepository('AppBundle:Civilite')
			        	->find($assocCivilite->getNewId());
			        $organisme->setCivilite($civilite);		        	
		        }


		        // dump($organisme);

				$em->persist($organisme);
				$em->flush();

				$this->createAssoc($em,$data[0],$organisme->getId(),'organisme');
	        }
	    }
	    dump('==== organisme imported');
    }

    public function importContact($path,$em){
		
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('contact-adherent');

		$handle = fopen('import/'.$path.'/'.$this->files['adherents'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 5000, ",")) !== FALSE && $row < 200) {
	        if(intval($data[0]) != 0  && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $contact = new Contact();
		        
		        $contact->populateFromCSV($data); 

		        $assocSection = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'section'));
		        if($assocSection){
			        $section = $this->getDoctrine()
			        	->getRepository('AppBundle:Section')
			        	->find($assocSection->getNewId());
			        $contact->setSection($section);		        	
		        }

		        $assocStatutJuridique = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[21],'entity'=>'statutJuridique'));
		        if($assocStatutJuridique){
			        $statutJuridique = $this->getDoctrine()
			        	->getRepository('AppBundle:StatutJuridique')
			        	->find($assocStatutJuridique->getNewId());
			        $contact->setStatutJuridique($statutJuridique);		        	
		        }

				$em->persist($contact);
				$em->flush();

				$this->createAssoc($em,$data[0],$contact->getId(),'contact-adherent');
	        }
	    }

	    //flush to have assocs ready
	    $em->flush();

    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('contact-membre');

	    $handle = fopen('import/'.$path.'/'.$this->files['membres'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 200) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);

		        if ($data[2] == "True") {
		        	// if membre principal
		        	$assocContact = $this->getDoctrine()
			        	->getRepository('AppBundle:AssocImport')
			        	->findOneBy(array('oldId'=>$data[1],'entity'=>'contact-adherent'));
			        if($assocContact){
				        $contact = $this->getDoctrine()
				        	->getRepository('AppBundle:Contact')
				        	->find($assocContact->getNewId());
			        	
			        	$contact->populateMembreFromCSV($data);

			        	$assocFoncGroupement = $this->getDoctrine()
				        	->getRepository('AppBundle:AssocImport')
				        	->findOneBy(array('oldId'=>$data[4],'entity'=>'fonctionGroupement'));
				        if($assocFoncGroupement){
					        $foncGroupement = $this->getDoctrine()
					        	->getRepository('AppBundle:FonctionGroupement')
					        	->find($assocFoncGroupement->getNewId());
					        $contact->setFonctionGroupement($foncGroupement);		        	
				        }
				        
			        	$assocFoncSection = $this->getDoctrine()
				        	->getRepository('AppBundle:AssocImport')
				        	->findOneBy(array('oldId'=>$data[5],'entity'=>'fonctionSection'));
				        if($assocFoncSection){
					        $foncSection = $this->getDoctrine()
					        	->getRepository('AppBundle:FonctionSection')
					        	->find($assocFoncSection->getNewId());
					        $contact->setFonctionSection($foncSection);		        	
				        }

			        	$assocCivilite = $this->getDoctrine()
				        	->getRepository('AppBundle:AssocImport')
				        	->findOneBy(array('oldId'=>$data[10],'entity'=>'civilite'));
				        if($assocCivilite){
					        $civilite = $this->getDoctrine()
					        	->getRepository('AppBundle:Civilite')
					        	->find($assocCivilite->getNewId());
					        $contact->setCivilite($civilite);		        	
				        }

			        	$assocMatrim = $this->getDoctrine()
				        	->getRepository('AppBundle:AssocImport')
				        	->findOneBy(array('oldId'=>$data[15],'entity'=>'statutMatrimonial'));
				        if($assocMatrim){
					        $matrim = $this->getDoctrine()
					        	->getRepository('AppBundle:StatutMatrimonial')
					        	->find($assocMatrim->getNewId());
					        $contact->setStatutMatrimonial($matrim);		        	
				        }



			        	$em->persist($contact);
						$this->createAssoc($em,$data[0],$contact->getId(),'contact-membre');
						$em->flush();
			        }
		        }else{
		        	// if conjoint
			        $contact = new Contact();
			        $contact->populateMembreFromCSV($data);
		        	$maxNumAdh = $this->getDoctrine()
			          ->getRepository('AppBundle:Contact')
			          ->findMaxNumAdh();
			        $contact->setNumAdh($maxNumAdh+1);

			        $assocContact = $this->getDoctrine()
			        	->getRepository('AppBundle:AssocImport')
			        	->findOneBy(array('oldId'=>$data[1],'entity'=>'contact-adherent'));
			        if($assocContact){
				        $conjoint = $this->getDoctrine()
				        	->getRepository('AppBundle:Contact')
				        	->find($assocContact->getNewId());
				        $contact->setMembreConjoint($conjoint);	

				        $contact->setStatutJuridique(
				        	$this->getDoctrine()
				        	->getRepository('AppBundle:StatutJuridique')
				        	->findOneBy(array('label'=>'Membre Conjoint'))
			        	);	

			        	$assocFoncGroupement = $this->getDoctrine()
				        	->getRepository('AppBundle:AssocImport')
				        	->findOneBy(array('oldId'=>$data[4],'entity'=>'fonctionGroupement'));
				        if($assocFoncGroupement){
					        $foncGroupement = $this->getDoctrine()
					        	->getRepository('AppBundle:FonctionGroupement')
					        	->find($assocFoncGroupement->getNewId());
					        $contact->setFonctionGroupement($foncGroupement);		        	
				        }
				        
			        	$assocFoncSection = $this->getDoctrine()
				        	->getRepository('AppBundle:AssocImport')
				        	->findOneBy(array('oldId'=>$data[5],'entity'=>'fonctionSection'));
				        if($assocFoncSection){
					        $foncSection = $this->getDoctrine()
					        	->getRepository('AppBundle:FonctionSection')
					        	->find($assocFoncSection->getNewId());
					        $contact->setFonctionSection($foncSection);		        	
				        }

			        	$assocCivilite = $this->getDoctrine()
				        	->getRepository('AppBundle:AssocImport')
				        	->findOneBy(array('oldId'=>$data[10],'entity'=>'civilite'));
				        if($assocCivilite){
					        $civilite = $this->getDoctrine()
					        	->getRepository('AppBundle:Civilite')
					        	->find($assocCivilite->getNewId());
					        $contact->setCivilite($civilite);		        	
				        }

			        	$assocMatrim = $this->getDoctrine()
				        	->getRepository('AppBundle:AssocImport')
				        	->findOneBy(array('oldId'=>$data[15],'entity'=>'statutMatrimonial'));
				        if($assocMatrim){
					        $matrim = $this->getDoctrine()
					        	->getRepository('AppBundle:StatutMatrimonial')
					        	->find($assocMatrim->getNewId());
					        $contact->setStatutMatrimonial($matrim);		        	
				        }
        	
				        $contact->setIsActif($conjoint->getIsActif());
				        $contact->setSection($conjoint->getSection());
				        $contact->setCommune($conjoint->getCommune());
				        $contact->setCp($conjoint->getCp());
				        $contact->setTelFixe($conjoint->getTelFixe());
				        $contact->setIsRentier(false);
				        $contact->setIsCourrier(false);
				        $contact->setIsBI(false);
				        $contact->setIsCA(false);
				        $contact->setIsOffreDecouverte(false);
				        $contact->setIsEnvoiIndiv(false);
				        $contact->setIsDossierPaye(false);
			        	
						$em->persist($contact);
						$em->flush();

						$conjoint->setMembreConjoint($contact);
						$em->persist($conjoint);
						$em->flush();

						$this->createAssoc($em,$data[0],$contact->getId(),'contact-membre');
			        }
		        }
	        }
	    }


	    dump('==== contacts imported');
    }


    public function importDon($path,$em){

    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('don');

		$handle = fopen('import/'.$path.'/'.$this->files['dons'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $don = new Don();
		        $don->populateFromCSV($data);

		        $assocContact = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'contact-adherent'));
		        if($assocContact){
			        $contact = $this->getDoctrine()
			        	->getRepository('AppBundle:Contact')
			        	->find($assocContact->getNewId());
			        $don->setContact($contact);
		        }

		        $assocMP = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[7],'entity'=>'moyenPaiement'));
		        if($assocMP){
			        $mp = $this->getDoctrine()
			        	->getRepository('AppBundle:MoyenPaiement')
			        	->find($assocMP->getNewId());
			        $don->setMoyenPaiement($mp);
		        }

		        // dump($don);

				$em->persist($don);
				$em->flush();

				$this->createAssoc($em,$data[0],$don->getId(),'don');
	        }
	    }
	    dump('==== dons imported');
    }

    public function importProcuration($path,$em){
		
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('procuration');

		$handle = fopen('import/'.$path.'/'.$this->files['procurations'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $proc = new Procuration();
		        $proc->populateFromCSV($data);

		        $assocContact = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'contact-adherent'));
		        if($assocContact){
			        $contact = $this->getDoctrine()
			        	->getRepository('AppBundle:Contact')
			        	->find($assocContact->getNewId());
			        $proc->setContact($contact);
		        }

		        // dump($proc);

				$em->persist($proc);
				$em->flush();

				$this->createAssoc($em,$data[0],$proc->getId(),'procuration');
	        }
	    }
	    dump('==== dons imported');
    }

    public function importVignette($path,$em){
		
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('vignette');

		$handle = fopen('import/'.$path.'/'.$this->files['vignettes'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $vignette = new Vignette();
		        $vignette->populateFromCSV($data);

		        $assocContact = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'contact-adherent'));
		        if($assocContact){
			        $contact = $this->getDoctrine()
			        	->getRepository('AppBundle:Contact')
			        	->find($assocContact->getNewId());
			        $vignette->setContact($contact);
		        }

		        $assocMP = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[4],'entity'=>'moyenPaiement'));
		        if($assocMP){
			        $mp = $this->getDoctrine()
			        	->getRepository('AppBundle:MoyenPaiement')
			        	->find($assocMP->getNewId());
			        $vignette->setMoyenPaiement($mp);
		        }

		        // dump($vignette);

				$em->persist($vignette);
				$em->flush();

				$this->createAssoc($em,$data[0],$vignette->getId(),'vignette');
	        }
	    }
	    dump('==== vignette imported');
    }

    public function importPouvoir($path,$em){

    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('pouvoir');

		$handle = fopen('import/'.$path.'/'.$this->files['pouvoirs'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $pouvoir = new Pouvoir();
		        $pouvoir->populateFromCSV($data);

		        $assocContact = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'contact-adherent'));
		        if($assocContact){
			        $contact = $this->getDoctrine()
			        	->getRepository('AppBundle:Contact')
			        	->find($assocContact->getNewId());
			        $pouvoir->setContact($contact);
		        }

		        // dump($pouvoir);

				$em->persist($pouvoir);
				$em->flush();

				$this->createAssoc($em,$data[0],$pouvoir->getId(),'pouvoir');
	        }
	    }
	    dump('==== pouvoir imported');
    }

    public function importContratPrevObs($path,$em){
		
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('contratPrevObs');

		$handle = fopen('import/'.$path.'/'.$this->files['obseques'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $cpo = new ContratPrevObs();
		        $cpo->populateFromCSV($data);

		        $assocContact = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[2],'entity'=>'contact-membre'));
		        if($assocContact){
			        $contact = $this->getDoctrine()
			        	->getRepository('AppBundle:Contact')
			        	->find($assocContact->getNewId());
			        $cpo->setContact($contact);
		        }


		        $arr = array(
					200	=> 0,
					201	=> 600,
					202	=> 1050,
					203	=> 1500,
					204	=> 2100,
					205	=> 2550,
					206	=> 3000,
					207	=> 4500,
					208	=> 6000,
					209	=> 7500,
				);

				$cpo->setOption($arr[$data[4]]);

		        // dump($cpo);

				$em->persist($cpo);
				$em->flush();

				$this->createAssoc($em,$data[0],$cpo->getId(),'contratPrevObs');
	        }
	    }
	    dump('==== contratPrevObs imported');
    }

    public function importContratPrevoyance($path,$em){
		
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('ContratPrevoyance');

		$handle = fopen('import/'.$path.'/'.$this->files['prevoyances'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 100) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        //CP1
		        $cp = new ContratPrevoyance();
		        $cp->populateFromCSV($data);

		        $assocContact = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'contact-membre'));
		        if($assocContact){
			        $contact = $this->getDoctrine()
			        	->getRepository('AppBundle:Contact')
			        	->find($assocContact->getNewId());
			        $cp->setContact($contact);
		        }

		        $assocRA = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[8],'entity'=>'regimeAffiliation'));
		        if($assocRA){
			        $ra = $this->getDoctrine()
			        	->getRepository('AppBundle:RegimeAffiliation')
			        	->find($assocRA->getNewId());
			        $cp->setRegimeAffiliation($ra);
		        }

				$em->persist($cp);
				$em->flush();

				$this->createAssoc($em,$data[0],$cp->getId(),'contratPrevoyance');

		        if ($data[11] != "") {

			        //CP2
					$cp2 = new ContratPrevoyance();
			        $cp2->populateFromCSV2($data);

			        $assocContact = $this->getDoctrine()
			        	->getRepository('AppBundle:AssocImport')
			        	->findOneBy(array('oldId'=>$data[1],'entity'=>'contact-membre'));
			        if($assocContact){
				        $contact = $this->getDoctrine()
				        	->getRepository('AppBundle:Contact')
				        	->find($assocContact->getNewId());
				        $cp2->setContact($contact);
			        }

			        $assocRA = $this->getDoctrine()
			        	->getRepository('AppBundle:AssocImport')
			        	->findOneBy(array('oldId'=>$data[15],'entity'=>'regimeAffiliation'));
			        if($assocRA){
				        $ra = $this->getDoctrine()
				        	->getRepository('AppBundle:RegimeAffiliation')
				        	->find($assocRA->getNewId());
				        $cp2->setRegimeAffiliation($ra);
			        }

			        // dump($cp2);

					$em->persist($cp2);
					$em->flush();

					$this->createAssoc($em,$data[0],$cp2->getId(),'contratPrevoyance');
		        }
	        }
	    }
	    dump('==== contratPrev imported');
    }

    public function importCotisation($path,$em){
		
		$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('cotisation');

		$handle = fopen('import/'.$path.'/'.$this->files['cotisations2'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0])>$maxId){
		        // $row++;
		        $this->incrementsCount();
				set_time_limit(20);
		        $cotisation = new Cotisation();
		        $cotisation->populateFromCSV($data);

		        $assocContact = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'contact-adherent'));
		        if($assocContact){
			        $contact = $this->getDoctrine()
			        	->getRepository('AppBundle:Contact')
			        	->find($assocContact->getNewId());
			        $cotisation->setContact($contact);
		        }

		        // dump($cotisation);

				$em->persist($cotisation);
				$em->flush();

				$this->createAssoc($em,$data[0],$cotisation->getId(),'cotisation');
	        }
	    }
	    dump('==== cotisation imported');
    }

    public function importSuivi($path,$em){
		//Etats

    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('suivi-etat');

		$handle = fopen('import/'.$path.'/'.$this->files['etats'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId && sizeof($data)>6){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $suivi = new Suivi();
		        $suivi->populateEtatFromCSV($data);

		        $assocContact = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'contact-adherent'));
		        if($assocContact){
			        $contact = $this->getDoctrine()
			        	->getRepository('AppBundle:Contact')
			        	->find($assocContact->getNewId());
			        $suivi->setContact($contact);
		        }

		        $suivi->setOperateur($this->getUser());
		        $suivi->setIsOk(true);


		        // // dump($suivi);

				$em->persist($suivi);
				$em->flush();

				$this->createAssoc($em,$data[0],$suivi->getId(),'suivi-etat');
	        }
	    }
		//Suivis
		
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('suivi');

		$handle = fopen('import/'.$path.'/'.$this->files['suivis'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 50) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId && sizeof($data)>10){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $suivi = new Suivi();
		        $suivi->populateSuiviFromCSV($data);

		        $assocContact = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[5],'entity'=>'contact-adherent'));
		        if($assocContact){
			        $contact = $this->getDoctrine()
			        	->getRepository('AppBundle:Contact')
			        	->find($assocContact->getNewId());
			        $suivi->setContact($contact);
		        }

		        $suivi->setOperateur($this->getUser());
		        $suivi->setIsOk(true);

		        // dump($suivi);

				$em->persist($suivi);
				$em->flush();

				$this->createAssoc($em,$data[0],$suivi->getId(),'suivi');
	        }
	    }
	    dump('==== suivi imported');
    }


    public function importDiplome($path,$em){
		
    	$maxId = $this->getDoctrine()
    		 ->getRepository('AppBundle:AssocImport')
    		 ->findMaxId('contactDiplome');

		$handle = fopen('import/'.$path.'/'.$this->files['diplomes'], 'r');
	    $row = 1;
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $row < 200) {
	        if(intval($data[0]) != 0 && intval($data[0]) > $maxId){
		        // $row++;
		        $this->incrementsCount();
		        set_time_limit(20);
		        $cd = new ContactDiplome();
		        $cd->populateFromCSV($data);

		        $assocContact = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[1],'entity'=>'contact-adherent'));
		        if($assocContact){
			        $contact = $this->getDoctrine()
			        	->getRepository('AppBundle:Contact')
			        	->find($assocContact->getNewId());
			        $cd->setContact($contact);
		        }

		        $assocDiplome = $this->getDoctrine()
		        	->getRepository('AppBundle:AssocImport')
		        	->findOneBy(array('oldId'=>$data[2],'entity'=>'diplome'));
		        if($assocDiplome){
			        $diplome = $this->getDoctrine()
			        	->getRepository('AppBundle:Diplome')
			        	->find($assocDiplome->getNewId());
			        $cd->setDiplome($diplome);
		        }


		        // dump($cd);

				$em->persist($cd);
				$em->flush();

				$this->createAssoc($em,$data[0],$cd->getId(),'contactDiplome');
	        }
	    }
	    dump('==== contactDiplome imported');
    }

    public function incrementsCount(){
    	$this->count = $this->count + 1;
    	if($this->count > 200){
    		throw new \LengthException("more than 200 lines", 1);
    	}
    }

    /**
     * @Route("/set-section-num", name="set_section_num")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function setSectionNumAction()
    {

    	$sections = $this->getDoctrine()
    		->getRepository('AppBundle:Section')
    		->findAll();

    	foreach ($sections as $section) {
    		$assoc = $this->getDoctrine()
    			->getRepository('AppBundle:AssocImport')
    			->findOneBy(array('newId'=>$section->getId(),'entity'=>'section'));
    		$section->setNum($assoc->getOldId());
    		$em = $this->get('doctrine.orm.entity_manager');
    		$em->persist($section);
    	}

    	$em->flush();

        return new Response('ok');

    }

}