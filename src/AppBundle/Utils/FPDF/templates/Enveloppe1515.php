<?php

namespace AppBundle\Utils\FPDF\templates;

use AppBundle\Utils\FPDF\FPDF;

class Enveloppe1515 extends FPDF {

	 function __construct()
	 {
	 	parent::__construct('L','mm',array('150','150'));
	 }

	function AddDest($lines=array()){
	 	
	 	$x = ($this->GetPageWidth()/2-20);
	 	$y = ($this->GetPageHeight()/2);

	 	$this->SetLeftMargin($x);
	 	$this->SetY($y);
	 	foreach ($lines as $line) {
	 		$this->Cell(40,5,utf8_decode($line));
	 		$this->Ln();
	 	}
	}
}