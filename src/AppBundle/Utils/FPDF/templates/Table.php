<?php

namespace AppBundle\Utils\FPDF\templates;

use AppBundle\Utils\FPDF\templates\DefaultModel;

class Table extends DefaultModel {

    public function EnteteCerfa($num,$entete){
        $this->Image('img/cerfa.png',$this->getX()+17,$this->getY(),20);
        $this->SetLeftMargin(15);
        $this->setY($this->getY()+10);
        $this->SetFont('','B',11);
        $this->Cell(20,5,utf8_decode($num));
        $this->SetY($this->getY()-5);
        $this->SetLeftMargin(45);
        $this->SetFont('','',10);
        $this->Cell(0,10,utf8_decode($entete));
        $this->Ln(12);
    }

    public function TableLine($title,$content,$size='1'){
        
        $totalWidth = $this->GetPageWidth()-12-$this->GetX();
        $width = $totalWidth;
        $height=23;
        switch ($size) {
            case '1':
                $width = $totalWidth;
                break;
            case '1/2':
                $width = $totalWidth/2;
                break;
            default:
                case '1';
                break;
        }

        $startX = $this->getX();
        $startY = $this->getY();
        if($size=='1'){
            $this->Rect($startX,$startY,$width,$height);
            $this->SetY($startY+3);
            $this->SetX($startX+3);
            $this->SetFont('','B',10);
            $this->Cell($size,6,utf8_decode($title));
            $this->Ln();
            $this->SetX($startX+3);
            $this->SetFont('','',9);
            $this->MultiCell(0,4,utf8_decode(str_replace('\n', "\n", $content)));
        }
        if($size=='1/2'){
            $this->Rect($startX,$startY,$width,$height);
            $this->SetY($startY+6);
            $this->SetX($startX+3);
            $this->SetFont('','',10);
            $this->MultiCell(0,4,utf8_decode($title));
            
            $this->Rect($startX+$width,$startY,$width,$height);
            $this->SetY($startY+6);
            $this->SetX($startX+$width+3);
            $this->SetFont('','B',10);
            $this->MultiCell($width-10,4,utf8_decode(str_replace('\n', "\n", $content)));
        }


        $this->SetX($startX);
        $this->SetY($startY+$height);
    }

    function specTableLine($label1,$label2){
        $totalWidth = $this->GetPageWidth()-12-$this->GetX();
        $height=23;

        $startX = $this->getX();
        $startY = $this->getY();

        $this->Rect($startX,$startY,$totalWidth/2,$height/2);
        $this->SetY($startY+5);
        $this->SetX($startX+3);
        $this->SetFont('','',10);
        $this->Cell($totalWidth/2,4,utf8_decode($label1));

        $this->Rect($startX,$startY+$height/2,$totalWidth/2,$height/2);
        $this->SetY($startY+($height/2)+5);
        $this->SetX($startX+3);
        $this->SetFont('','',10);
        $this->Cell($totalWidth/2,4,utf8_decode($label2));
        
        $this->Rect($startX+($totalWidth/2),$startY,$totalWidth/2,$height);
        $this->SetY($startY+2);
        $this->SetX($startX+($totalWidth/2)+3);
        $this->SetFont('','',10);
        $this->Cell($totalWidth/2,4,utf8_decode('Signature : '));

        $this->SetX($startX);
        $this->SetY($startY+$height);
    }
  

}