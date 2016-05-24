<?php

namespace AppBundle\Utils\FPDF\templates;

use AppBundle\Utils\FPDF\FPDF;

class DefaultModel extends FPDF {


    function Header(){

        $date = new \DateTime();

        $this->Image('img/logo-smaller.png',8,8,25);
        $this->SetDrawColor(183,46,75);
        $this->Line(40,12,40,28);
        $this->SetTextColor(51,51,51);
        $this->SetFont('Helvetica','B',10);
        $this->SetLeftMargin(45);
        $this->MultiCell('80','7',utf8_decode('UNIAT ALSACE'));
        $this->SetFont('Helvetica','',8);
        $this->MultiCell('80','3',utf8_decode("28, Rue du Fbg de Saverne\n67000 Strasbourg\ntél. 03 88 15 00 05\nuniat@uniat-alsace.fr"));
        $this->SetLeftMargin(40);
        $this->SetY(7);
        $this->SetFont('Helvetica','B',14);
        $this->Cell('','10',$date->format('Y'),0,2,'R');
        $this->SetY(20);
        $this->SetFont('Helvetica','B',10);
        $this->SetTextColor(47,71,146);
        $this->Cell('','5',utf8_decode('ISOLÉ VOUS ÊTES SANS DÉFENSE,'),0,2,'R');
        $this->Cell('','5',utf8_decode('UNIS, VOUS ÊTES UNE GRANDE FORCE,'),0,2,'R');
        $this->SetY(40);
        $this->SetX(12);
    }

    function Footer(){
        $this->SetY($this->GetPageHeight()-15);
        $this->SetDrawColor(183,46,75);
        $this->Line(12,$this->GetY(),$this->GetPageWidth()-12,$this->GetY());
        $this->SetTextColor(183,46,75);
        $this->SetFont('Helvetica','B',9);
        $this->Cell('',10,'www.uniat-alsace.fr',null,null,'R');
    }

    function RightText($txt){
        $this->Ln(10);
        $this->SetFont('Helvetica','',9);
        $this->MultiCell('',5,utf8_decode($txt),null,'R');
        $this->Ln(10);
    }

    function GreyJoyBlock($fields,$header=false,$cols=2){

        $this->SetLeftMargin(12);
        $this->Ln(2);
        $this->SetFillColor(235,235,235);
        $this->SetTextColor(51,51,51);
        $this->SetFont('Helvetica','',9);
        $w = ($this->getPageWidth()-24)/2;
        $cpt=0;

        if($header){
            $this->SetFont('Helvetica','B',10);
            $this->Cell($w*2,10,'  '.$header,0,0,'L',true);
            $this->Ln(9);
            $this->SetFont('Helvetica','',9);
        }

        foreach($fields as $field => $value)
        {
            $cpt++;
            $this->SetLeftMargin(12);
            $this->Cell($w,10,($field!=''?'  '.utf8_decode($field).' : ':'  ').utf8_decode($value),0,0,'L',true);
            if($cpt%$cols==0){
                $this->Ln();
            }
        }
        //fill the last cells
        if($cpt%$cols!=0){
            for ($i=0; $i < $cols-$cpt%$cols; $i++) { 
                $this->Cell($w,10,' ',0,0,'L',true);
            }
        }
        $this->Ln(2);
    }

    function Title($title){
        $this->SetFont('Helvetica','B',12);
        $this->SetFontDefault(51,51,51);
        $this->SetDrawColor(51,51,51);
        $this->Line(12,$this->GetY(),$this->GetPageWidth()-12,$this->GetY());
        $this->Cell(0,10,utf8_decode($title),0,2,'C');
        $this->Line(12,$this->GetY(),$this->GetPageWidth()-12,$this->GetY());
        $this->Ln(2);
        $this->SetX(0);
    }

    function AddParagraphe($p){
        $this->Ln();
        $this->WriteHTML(utf8_decode($p));
        $this->Ln();
    }

    function AddComponent($field,$value=false,$type='text',$format='1/3'){
        $this->SetLeftMargin(12);
        $this->SetFont('Helvetica','B',10);
        if($type=='text'){
            $this->Cell($this->GetSizeByFormat($format)/2,10,utf8_decode($field));
            $this->SetFont('Helvetica','',9);
            $this->Cell($this->GetSizeByFormat($format)/2,10,utf8_decode($value));
        }elseif ($type=='cb') {
            $this->AddCheckBox();
            $this->SetX($this->GetX()+5);
            $this->Cell($this->GetSizeByFormat($format)-5,10,utf8_decode($field));
        }
    }

    function GetSizeByFormat($format){
        $trueWidth = $this->GetPageWidth()-24;
        switch ($format) {
            case '1/3':
                return $trueWidth/3;
                break;
            case '1/2':
                return $trueWidth/2;
                break;
            case '1/4':
                return $trueWidth/4;
                break;
            case '1':
                return $trueWidth;
                break;
            default:
                return $trueWidth;
                break;
        }
    }

    function CotisationBlock(){
        $width = 60;
        $this->Ln(2);
        $this->SetFont('','',9);
        $this->SetFillColor(235,235,235);
        $this->Cell($width,10,utf8_decode('  Membre à compter du : '),null,1,null,true);
        $this->SetFont('','B',10);
        $this->Cell(10,10,' ',null,0,null,true);
        $this->SetX(15);
        $this->AddCheckBox();
        $this->SetX(22);
        $this->Cell($width-10,10,'01/01/20___',null,1,null,true);
        $this->Cell(10,10,' ',null,0,null,true);
        $this->SetX(15);
        $this->AddCheckBox();
        $this->SetX(22);
        $this->Cell($width-10,10,'01/07/20___',null,1,null,true);
        $this->SetFont('','',9);
        $this->Cell($width,10,utf8_decode('  Droit d\'entrée : 10 euros'),null,1,null,true);
        $this->Cell($width,10,utf8_decode('  Cotisation annuelle : '),null,1,null,true);
        $this->Cell($width,10,utf8_decode('  Cotisation semestrielle : '),null,1,null,true);
        $this->Cell($width,10,utf8_decode('  Ouverture d\'un dossier : 20 euros'),null,1,null,true);
        $this->SetFont('','B',10);
        $this->Cell($width,14,utf8_decode('  TOTAL'),null,null,null,true);
        $this->Rect($this->GetX()-30,$this->GetY()+2,26,9);
    }

    function SpecialCases($label){
        $this->Ln(2);
        $this->SetFont('','',9);
        $this->MultiCell(40,5,utf8_decode($label));
        $this->Image('img/case-form.jpg',$this->GetX()+35,$this->GetY()-5,83);
        $this->Ln(2);
    }

    function Signature($label1,$label2){
        $this->Ln(2);
        $this->SetFont('','',9);
        $this->Cell(60,10,utf8_decode($label1));
        $this->Ln();
        $this->Rect($this->GetX(),$this->GetY(),55,25);
        $this->Cell(60,10,utf8_decode('  '.$label2));
        $this->SetY($this->GetY()+40);
    }

    function AddCheckBox($checked=false){
        $this->Rect($this->GetX()+1,$this->GetY()+3,3,3);
    }

    function SetFontUniatBlue(){
        $this->SetTextColor(56,73,145);
    }

    function SetFontDefault(){
        $this->SetTextColor(51,51,51);
    }


    protected $B = 0;
    protected $I = 0;
    protected $U = 0;
    protected $HREF = '';

    function WriteHTML($html)
    {
        // Parseur HTML
        $html = str_replace("\n",' ',$html);
        $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                // Texte
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(5,$e);
            }
            else
            {
                // Balise
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    // Extraction des attributs
                    $a2 = explode(' ',$e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }

    function OpenTag($tag, $attr)
    {
        // Balise ouvrante
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF = $attr['HREF'];
        if($tag=='BR')
            $this->Ln(5);
    }

    function CloseTag($tag)
    {
        // Balise fermante
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF = '';
    }

    function SetStyle($tag, $enable)
    {
        // Modifie le style et sélectionne la police correspondante
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach(array('B', 'I', 'U') as $s)
        {
            if($this->$s>0)
                $style .= $s;
        }
        $this->SetFont('',$style);
    }

    function PutLink($URL, $txt)
    {
        // Place un hyperlien
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }

}