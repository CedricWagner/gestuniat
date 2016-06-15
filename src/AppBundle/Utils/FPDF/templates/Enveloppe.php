<?php

namespace AppBundle\Utils\FPDF\templates;

use AppBundle\Utils\FPDF\FPDF;

class Enveloppe extends FPDF {

	 function __construct()
	 {
	 	parent::__construct('L','mm','A4');
	 }

	 function AddDest($lines=array()){
	 	
	 	$x = ($this->GetPageWidth()/2)-40;
	 	$y = ($this->GetPageHeight()/2)-40;

	 	$this->SetLeftMargin($x);
	 	$this->SetY($y);
	 	foreach ($lines as $line) {
	 		$this->Cell(40,10,utf8_decode($line));
	 		$this->Ln();
	 	}
	 }
}