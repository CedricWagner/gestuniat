<?php

namespace AppBundle\Utils;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Tools extends Controller
{

	function __construct() {
	
	}

	public function getErrorMessages($form){
		
		$errors = array();

	    foreach ($form->getErrors() as $key => $error) {
	        if ($form->isRoot()) {
	            $errors['#'][] = $error->getMessage();
	        } else {
	            $errors[] = $error->getMessage();
	        }
	    }

		foreach ($form->all() as $child) {
	        if (!$child->isValid()) {
	        	dump($child);
	            $errors[$child->getName()] = $this->getErrorMessages($child);
	        }
	    }

	    return $errors;
	}

	public function handleFormErrors($form){

		foreach ($this->getErrorMessages($form) as $field => $error) {
	        $this->get('session')->getFlashBag()->add('danger', $field.' : '.$error[0]);
		}
	}

}