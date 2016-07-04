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

	public function asLetters($number,$root=false) {
	    $convert = explode('.', $number);
	    $num[17] = array('zero', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit',
	                     'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize');
	                    
	    $num[100] = array(20 => 'vingt', 30 => 'trente', 40 => 'quarante', 50 => 'cinquante',
	                      60 => 'soixante', 70 => 'soixante-dix', 80 => 'quatre-vingt', 90 => 'quatre-vingt-dix');
	                    
	    if (isset($convert[1]) && $convert[1] != '') {
	      return $this->asLetters(intval($convert[0])).' euros et '.$this->asLetters(intval($convert[1])).' centimes';
	    }
	    if($number=='00'){
	    	$number = 0;
	    }
	    if ($number < 0) return 'moins '.$this->asLetters(-$number);
	    if ($number < 17) {
	      return $num[17][$number];
	    }
	    elseif ($number < 20) {
	      return 'dix-'.$this->asLetters($number-10);
	    }
	    elseif ($number < 100) {
	      if ($number%10 == 0) {
	        return $num[100][$number];
	      }
	      elseif (substr($number, -1) == 1) {
	        if( ((int)($number/10)*10)<70 ){
	          return $this->asLetters((int)($number/10)*10).'-et-un';
	        }
	        elseif ($number == 71) {
	          return 'soixante-et-onze';
	        }
	        elseif ($number == 81) {
	          return 'quatre-vingt-un';
	        }
	        elseif ($number == 91) {
	          return 'quatre-vingt-onze';
	        }
	      }
	      elseif ($number < 70) {
	        return $this->asLetters($number-$number%10).'-'.$this->asLetters($number%10);
	      }
	      elseif ($number < 80) {
	        return $this->asLetters(60).'-'.$this->asLetters($number%20);
	      }
	      else {
	        return $this->asLetters(80).'-'.$this->asLetters($number%20);
	      }
	    }
	    elseif ($number == 100) {
	      return 'cent';
	    }
	    elseif ($number < 200) {
	      return $this->asLetters(100).' '.$this->asLetters($number%100);
	    }
	    elseif ($number < 1000) {
	      return $this->asLetters((int)($number/100)).' '.$this->asLetters(100).'s'.($number%100 > 0 ? ' '.$this->asLetters($number%100): '');
	    }
	    elseif ($number == 1000){
	      return 'mille';
	    }
	    elseif ($number < 2000) {
	      return $this->asLetters(1000).' '.$this->asLetters($number%1000).' ';
	    }
	    elseif ($number < 1000000) {
	      return $this->asLetters((int)($number/1000)).' '.$this->asLetters(1000).($number%1000 > 0 ? ' '.$this->asLetters($number%1000): '');
	    }
	    elseif ($number == 1000000) {
	      return 'millions';
	    }
	    elseif ($number < 2000000) {
	      return $this->asLetters(1000000).' '.$this->asLetters($number%1000000);
	    }
	    elseif ($number < 1000000000) {
	      return $this->asLetters((int)($number/1000000)).' '.$this->asLetters(1000000).($number%1000000 > 0 ? ' '.$this->asLetters($number%1000000): '');
	    }
  	}

}