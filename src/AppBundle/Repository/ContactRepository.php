<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

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
			->where('contact.dateSortie IS NULL')
            ->setFirstResult(($nb*$page)-$nb)
            ->setMaxResults($nb*$page);

        $pag = new Paginator($qb);
        return $pag;
	}
	
	public function findByFilter($filterValues,$page=1,$nb=20){
		
		$params = array();
		
		$qb = $this->createQueryBuilder('contact');
		$qb
			->select('contact')
			->where('contact.dateSortie IS NULL');
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
					case 'section':
						//TODO
						break;
					case 'selFonctionSection':
						$qb->andwhere('contact.fonctionSection = :p_fonction_section');
						$params['p_fonction_section'] = $fv->getValeur();
						break;
					case 'selFonctionGroupement':
						$qb->andwhere('contact.fonctionGroupement = :p_fonction_groupement');
						$params['p_fonction_groupement'] = $fv->getValeur();
						break;
					case 'txtRepresentation':
						$qb->andwhere('contact.fonctionRepresentation LIKE :p_fonction_representation');
						$params['p_fonction_representation'] = $fv->getValeur();
						break;
					case 'selPaiement':
						//TODO
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
            ->setFirstResult(($nb*$page)-$nb)
            ->setMaxResults($nb*$page);

        $pag = new Paginator($qb);
        
        return $pag;
	}

	public function search($txtSearch, $exclude=false){


		$qb = $this->createQueryBuilder('contact');
		$qb
			->select('contact')
			->where('contact.dateSortie IS NULL')
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
}
