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

    function Bottom(){
        $this->Ln(7);
        $svgY = $this->GetY();
        $this->SetFont('','B');
        $this->Cell(90,10,utf8_decode('UNIAT - GROUPEMENT ALSACE'));
        $this->Ln();
        $this->SetFont('','');
        $this->MultiCell(90,5,utf8_decode("Association régionale à but non lucratif, créé en 1924, \ninscrite au registre des associations sous Vol. XIX n°12 \ndu tribunal d'Instance de Strasbourg. Associée à la FNATH."));
        
        $this->Ln(2);
        $this->SetFont('','B');
        $this->Cell(90,10,utf8_decode('SIÈGE SOCIAL'));
        $this->Ln();
        $this->SetFont('','');
        $this->MultiCell(90,5,utf8_decode("28 rue du faubourg de Saverne \n67 000 Strasbourg"));
        $this->Ln(2);
        $this->SetFont('','B');
        $this->SetFontUniatBlue();
        $this->MultiCell(90,5,utf8_decode("03 88 15 00 05 \nuniat@uniat-alsace.fr"));
        
        $this->Ln(2);
        $this->SetFontDefault();
        $this->SetFont('','B');
        $this->Cell(90,10,utf8_decode('UNION DES INVALIDES ET ACCIDENTÉ DU TRAVAIL'));
        $this->Ln();
        $this->SetFont('','');
        $this->MultiCell(90,5,utf8_decode("Handicapés, veuves, retraités et assurés sociaux de \ntous régimes"));

        $this->SetY($svgY+25);
        $this->SetLeftMargin(110);
        $this->Image('img/logo-smaller.png',$this->GetX()+32,$this->GetY(),25);
        $this->Ln(27);
        $this->SetFontUniatBlue();
        $this->SetFont('','B',14);
        $this->Cell(90,12,'UNIAT - ALSACE',0,2,'C');
        $this->SetFont('','B',10);
        $this->Cell('','5',utf8_decode('ISOLÉ VOUS ÊTES SANS DÉFENSE,'),0,2,'C');
        $this->Cell('','5',utf8_decode('UNIS, VOUS ÊTES UNE GRANDE FORCE,'),0,2,'C');

    }


}