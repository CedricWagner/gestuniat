<?php

namespace AppBundle\Utils\FPDF\templates;

use AppBundle\Utils\FPDF\FPDF;

class DefaultModel extends FPDF {

    function Header(){

        $date = new \DateTime();

        $this->Image('img/logo.png',10,10,30);
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


    }

    function Footer(){
        $this->SetTextColor(183,46,75);
    }

    function GreyJoyBlock(){
        $this->SetTextColor(230,230,230);
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