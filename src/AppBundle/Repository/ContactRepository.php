<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use AppBundle\Entity\StatutJuridique;

/**
 * ContactRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContactRepository extends \Doctrine\ORM\EntityRepository
{

	public function findAllWithPagination($page=1,$nb=20){
		

		$qb = $this->createQueryBuilder('contact');
		$qb
			->select('contact')
			->where('contact.isActif = true')
            ->setFirstResult(($nb*$page)-$nb)
            ->setMaxResults($nb*$page);

        $pag = new Paginator($qb);
        return $pag;
	}
	
	public function findByFilter($filterValues,$page=1,$nb=20,$orderby="numAdh",$order="ASC"){
		
		$params = array();
		
		$qb = $this->createQueryBuilder('contact');
		$qb
			->select('contact')
			->where('contact.isActif = true');
		foreach ($filterValues as $fv) {
			if($fv->getValeur()!=''&&$fv->getValeur()!='0'){
				switch ($fv->getChamp()->getLabel()) {
					case 'txtLocalisation':
							$qb->andwhere('contact.commune LIKE :p_commune');
							$params['p_commune'] = $fv->getValeur();
						break;
					case 'dateCreation':
						$qb->andwhere('contact.dateEntree = :p_date_entree');
						$params['p_date_entree'] = $fv->getValeur();
						break;
					case 'dateAnciennete':
						$qb->andwhere('contact.dateAdhesion >= :p_date_adhesion');
						$params['p_date_adhesion'] = $fv->getValeur();
						break;
					case 'cbStatut':
						$qb->andwhere('contact.statutJuridique = :p_statut_juridique');
						$params['p_statut_juridique'] = $fv->getValeur();
						break;
					case 'selSection':
						$qb->andwhere('contact.section = :p_section');
						$params['p_section'] = $fv->getValeur();
						break;
					case 'selFonctionSection':
						$qb->andwhere('contact.fonctionSection = :p_fonction_section');
						$params['p_fonction_section'] = $fv->getValeur();
						break;
					case 'selFonctionGroupement':
						if($fv->getValeur()=='TOUTES'){
							$qb->andwhere('contact.fonctionGroupement IS NOT NULL');
						}else{
							$params['p_fonction_groupement'] = $fv->getValeur();
						}
						break;
					case 'txtRepresentation':
						$qb->andwhere('contact.fonctionRepresentation LIKE :p_fonction_representation');
						$params['p_fonction_representation'] = $fv->getValeur();
						break;
					case 'cbCA':
						if($fv->getValeur()==1)
						$qb->andwhere('contact.isCA = true');
						break;
					case 'selPaiement':
						if($fv->getValeur() == 'V_PAYEE'){
							$qb->join('AppBundle:Vignette', 'vign', 'WITH', 'vign.contact = contact');
							$qb->andwhere('vign.datePaiement IS NOT NULL');
						}elseif($fv->getValeur() == 'V_RETARD'){
							$qb->join('AppBundle:Vignette', 'vign', 'WITH', 'vign.contact = contact');
							$qb->andwhere('vign.datePaiement IS NULL');
						}elseif($fv->getValeur() == 'DON'){
							$qb->join('AppBundle:Don', 'don', 'WITH', 'don.contact = contact');
						}
						break;
					case 'selDiplome':
						$qb->join('AppBundle:ContactDiplome', 'cd', 'WITH', 'cd.contact = contact');
						$qb->andwhere('cd.diplome = :p_diplome');
						$params['p_diplome'] = $fv->getValeur();
						break;
					case 'selPrevoyance':
						if ($fv->getValeur()=='OBS') {
							$qb->join('AppBundle:ContratPrevObs', 'cpo', 'WITH', 'cpo.contact = contact');
						}elseif($fv->getValeur()=='AGRR'){
							$qb->join('AppBundle:ContratPrevoyance', 'cp', 'WITH', 'cp.contact = contact');
						}
						break;
					case 'cbRentier':
						if ($fv->getValeur()=='RENTIER') {
							$qb->andwhere('contact.isRentier = :p_is_rentier');
							$params['p_is_rentier'] = true;
						}elseif ($fv->getValeur()=='DESTINATAIRE_INDIV') {
							$qb->andwhere('contact.isEnvoiIndiv LIKE :p_is_envoi_indiv');
							$params['p_is_envoi_indiv'] = true;
						}elseif ($fv->getValeur()=='OFFRE_DECOUVERTE') {
							$qb->andwhere('contact.isOffreDecouverte LIKE :p_is_offre_decouverte');
							$params['p_is_offre_decouverte'] = true;
						}
						break;
				}
			}
		}
		$qb ->setParameters($params)
            ->orderby('contact.'.$orderby,$order)
            ->setFirstResult(($nb*$page)-$nb)
            ->setMaxResults($nb);

        $pag = new Paginator($qb);
        
        return $pag;
	}

	public function search($txtSearch, $exclude=false){


		$qb = $this->createQueryBuilder('contact');
		$qb
			->select('contact')
			->where('contact.isActif = true')
			->andwhere('contact.nom LIKE :nom OR contact.prenom LIKE :prenom OR contact.numAdh = :numAdh')
			->setParameters(array('nom'=>$txtSearch.'%','prenom'=>$txtSearch.'%','numAdh'=>$txtSearch))
            ->setFirstResult(0)
            ->setMaxResults(20);

        $pag = new Paginator($qb);

        return $pag;
	}

	public function findMaxNumAdh(){
		$qb = $this->createQueryBuilder('contact');
		$result = $qb
			->select('MAX(contact.numAdh)')
			->getQuery()
    		->execute();

        return $result[0][1];
	}

	public function findFonctionsSection($section){
		$qb = $this->createQueryBuilder('contact');
		$result = $qb
			->select('contact')
			->join('AppBundle:FonctionSection','fs','WITH','contact.fonctionSection = fs')
			->where('contact.section = :p_section')
			->andwhere('contact.isActif = true')
			->setParameters(array('p_section'=>$section))
			->getQuery()
    		->execute();

        return $result;
	}

	public function countContactsBySection($section){
		return $this->createQueryBuilder('contact')
		 ->select('COUNT(contact)')
		 ->where('contact.section = :section')
		 ->andwhere('contact.statutJuridique = :statut')
		 ->andwhere('contact.isActif = true')
		 ->setParameters(array('section'=>$section,'statut'=>StatutJuridique::getIdStatutAdherent()))
		 ->getQuery()
		 ->getSingleScalarResult();

	}
}
