<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Contact;
use AppBundle\Utils\FPDF\templates\DefaultModel as PDF_DefaultModel;


class PDFGeneratorController extends Controller
{

    /**
     * @Route("pdf/contact/{idContact}/generer/bulletin-adh", name="generate_bulletin_adhesion")
     * @Security("has_role('ROLE_USER')")
     */
    public function generateBulletinAdhAction($idContact)
    {

      $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($idContact);

      $pdf = new PDF_DefaultModel();
      $pdf->AddPage();
      $pdf->Title('BULLETIN D\'ADHÉSION');
      $pdf->AddComponent('NOUVELLE ADHÉSION',false,'cb');
      $pdf->AddComponent('ADHÉSION POURSUIVIE',false,'cb');
      $pdf->AddComponent('N° ADHÉRENT',$contact->getNumAdh());
      $pdf->Ln();
      $pdf->GreyJoyBlock(array(
        "Section locale"=>$contact->getSection()?$contact->getSection()->getNom():'',
        "Nom de l'encaisseur"=>$contact->getEncaisseur()
      ));
      $pdf->GreyJoyBlock(array(
        "Nom / Prénom"=>$contact->getNom().' '.$contact->getPrenom(),
        "Nom J. Fille"=>$contact->getNomJeuneFille(),
        "Né(e) le"=>$contact->getDateNaissance()?$contact->getDateNaissance()->format('d/m/Y'):'',
        "Profession"=>''
      ),'MEMBRE');
      if($contact->getMembreConjoint()){
        $pdf->GreyJoyBlock(array(
          "Nom / Prénom"=>$contact->getMembreConjoint()->getNom().' '.$contact->getMembreConjoint()->getPrenom(),
          "Nom J. Fille"=>$contact->getMembreConjoint()->getNomJeuneFille(),
          "Né(e) le"=>$contact->getMembreConjoint()->getDateNaissance()?$contact->getMembreConjoint()->getDateNaissance()->format('d/m/Y'):'',
          "Profession"=>''
        ),'CONJOINT(E)');
      }
      $pdf->GreyJoyBlock(array(
        "Adresse"=>$contact->getAdresse(),
        "Téléphone"=>$contact->getTelFixe(),
        ""=>$contact->getAdresseComp(),
        "Portable"=>$contact->getTelPort(),
        "CP / Commune"=>$contact->getCp().' '.$contact->getCommune(),
        "E-mail"=>$contact->getMail()
      ));
      $svgY = $pdf->GetY();
      $pdf->CotisationBlock();
      $pdf->SetLeftMargin(80);
      $pdf->SetY($svgY);
      $pdf->SpecialCases("N° Sécurité Sociale : \nou autre régime");
      $pdf->SpecialCases("Conjoint : ");
      $svgY = $pdf->GetY();
      $pdf->SetLeftMargin(80);
      $pdf->Signature("Fait à : ", "Signature du comité : ");
      $pdf->SetY($svgY);
      $pdf->SetLeftMargin(142);
      $pdf->Signature("le : ", "Signature du membre : ");

      $response = new Response();
      $response->setContent($pdf->Output());
      // $response->setContent(file_get_contents('pdf/last-'.$this->getUser()->getId().'.pdf'));
      $response->headers->set(
         'Content-Type',
         'application/pdf'
      );

      return $response; 
    }


    /**
     * @Route("pdf/contact/{idContact}/generer/lettre-remerciement", name="generate_lettre_remerciement")
     * @Security("has_role('ROLE_USER')")
     */
    public function generateLettreRemAction($idContact)
    {

      $date = New \DateTime();

      $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($idContact);

      $pdf = new PDF_DefaultModel();
      $pdf->AddPage();
      $pdf->Title('LETTRE DE REMERCIEMENT');
      $pdf->RightText("Strasbourg,\nle ".$date->format('d/m')."\nSection : ".$contact->getSection()->getNom());
      $pdf->SetLeftMargin(40);
      $pdf->AddParagraphe('Cher Membre,');
      $pdf->AddParagraphe('Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet.');
      $pdf->AddParagraphe('Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet.');
      $pdf->AddParagraphe('Lorem Ipsum dolor sit amet. Lorem Ipsum dolor sit amet.');

      $response = new Response();
      $response->setContent($pdf->Output());
      // $response->setContent(file_get_contents('pdf/last-'.$this->getUser()->getId().'.pdf'));
      $response->headers->set(
         'Content-Type',
         'application/pdf'
      );

      return $response; 
    }

}
