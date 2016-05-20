<?php

namespace AppBundle\Utils;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Tools extends Controller
{

	function __construct() {
	
	}

	public function handleFormErrors($form){
		dump($form->getErrors(true));
		foreach ($form->getErrors(true) as $error) {
	        $this->get('session')->getFlashBag()->add('danger', $error->getMessageTemplate());
		}
	}

}