<?php

namespace AppBundle\Entity;

/**
* 
*/
class OperateurFactory 
{
	public function createForRegistration()
	{
		$operateur = new Operateur();

		return $operateur;
	}
}