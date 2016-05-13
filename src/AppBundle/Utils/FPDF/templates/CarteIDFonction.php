<?php

namespace AppBundle\Utils\FPDF\templates;

use AppBundle\Utils\FPDF\templates\DefaultModel;

class CarteIDFonction extends DefaultModel {

    function AddPrimaryLine($label,$value){

        $this->Ln(2);
        $this->SetFont('','B',10);
        $this->Cell(120,10,utf8_decode($label),0,1);
        $this->SetFont('','',9);
        $this->Cell(120,10,utf8_decode($value),0,1);
    }

    function AddDefaultLine($label,$value){

        $this->SetFont('','',9);
        $this->Cell(120,7,utf8_decode($label.''.$value),0,1);
    }

    function PhotoHolder(){

        $this->Rect($this->GetX()+20,$this->getY()+10,30,40);
        $this->Cell(70,55,'PHOTO',0,0,'C');
    }


    function Separator(){
        $this->Line(12,$this->GetY(),$this->GetPageWidth()-12,$this->GetY());
        $this->Ln(2);
        $this->Line(12,$this->GetY(),$this->GetPageWidth()-12,$this->GetY());
    }


}