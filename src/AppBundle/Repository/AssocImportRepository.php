<?php

namespace AppBundle\Repository;

/**
 * AssocImportRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AssocImportRepository extends \Doctrine\ORM\EntityRepository
{

	public function findMaxId($entity){
		$qb = $this->createQueryBuilder('assocImport');
		$result = $qb
			->select('MAX(assocImport.oldId)')
			->where('assocImport.entity LIKE :p_entity')
			->setParameters(array('p_entity'=>$entity))
			->getQuery()
    		->execute();

        return $result[0][1];
	}

}
