<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Historique;
use AppBundle\Entity\Suivi;
use Doctrine\ORM\EntityManager;

class HistoryManager
{

	private $operateur; 
	private $entity; 
	private $action; 
	private $em; 

	function __construct(EntityManager $entityManager) {
		$this->em = $entityManager;
	}

	function init($user,$entity=array(),$action){

		$this->operateur = $user;
		$this->entity = $entity;
		$this->action = $action;

		return $this;
	}

	function log($createSuivi = false,$txt=false){

		$datetime = new \DateTime();

		//create hisory
		$history = new Historique();
		$history->setOperateur($this->operateur);
		$history->setIdEntite($this->entity['id']);
		$history->setNomEntite($this->entity['name']);
		$history->setAction($this->action);
		$history->setDate($datetime);

		$this->em->persist($history);
		$this->em->flush();

		//create suivi
		$verbs = array('INSERT'=>'Création','UPDATE'=>'Édition','DELETE'=>'Supression');
		$enabledEntities = array('Contact','Section','Dossier');
		if($createSuivi && in_array($this->entity['name'], $enabledEntities)){
			$suivi = new Suivi();
			$suivi->setOperateur($this->operateur);
			$suivi->setDateCreation($datetime);
			$suivi->setTexte($txt?$txt:$verbs[$this->action]);
			$suivi->setIsOk(true);
			switch ($this->entity['name']) {
				case 'Contact':
					$contact = $this->em->getRepository('AppBundle:Contact')->find($this->entity['id']);
					$suivi->setContact($contact);
					break;
				case 'Section':
					$section = $this->em->getRepository('AppBundle:Section')->find($this->entity['id']);
					$suivi->setSection($section);
					break;
				case 'Dossier':
					$dossier = $this->em->getRepository('AppBundle:Dossier')->find($this->entity['id']);
					$suivi->setDossier($dossier);
					break;
			}

			$this->em->persist($suivi);
			$this->em->flush();
		}
	}
}