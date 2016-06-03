<?php

namespace AppBundle\Utils\FPDF\templates;

use AppBundle\Utils\FPDF\templates\DefaultModel;

class OrdreMission extends DefaultModel {

    public function RedSection($fields=array()){
        $this->SetTextColor(255,255,255);
        $this->SetFont('','B',10);
        $this->SetFillColor(195,48,75);
        $this->Rect($this->GetX(),$this->GetY(),$this->GetPageWidth()-24,13,'F');
        $this->SetFillColor(255,255,255);
        $this->SetY($this->GetY()+2.5);
        foreach ($fields as $field) {
            $this->Cell(strlen($field['label'])*2,8,' '.utf8_decode($field['label']));
            $this->Cell($field['size'],8,'',0,0,'',true);
        }

        $this->SetFontDefault();
    }

}