<?php

namespace AppBundle\Repository;

/**
 * ContratPrevoyanceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContratPrevoyanceRepository extends \Doctrine\ORM\EntityRepository
{
	public function findByContactAndConjoint($contact, $conjoint){

		$qb = $this->createQueryBuilder('contratPrevoyance');
		$result = $qb
			->select('contratPrevoyance')
			->where('contratPrevoyance.contact = :contact OR contratPrevoyance.contact = :conjoint')
			->setParameters(array('contact'=>$contact,'conjoint'=>$conjoint))
			->getQuery()
    		->execute();

        return $result;
	}
}
