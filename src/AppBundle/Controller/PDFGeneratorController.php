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
use AppBundle\Utils\FPDF\templates\CarteIDFonction as PDF_CarteIDFonction;


class PDFGeneratorController extends Controller
{

    /**
     * @Route("pdf/contact/{idContact}/generer/bulletin-adh", name="generate_bulletin_adhesion")
     * @Security("has_role('ROLE_USER')")
     */
    public function generateBulletinAdhAction($idContact)
    {

      $datetime = new \DateTime();

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
        ""=>''
      ),'MEMBRE');
      if($contact->getMembreConjoint()){
        $pdf->GreyJoyBlock(array(
          "Nom / Prénom"=>$contact->getMembreConjoint()->getNom().' '.$contact->getMembreConjoint()->getPrenom(),
          "Nom J. Fille"=>$contact->getMembreConjoint()->getNomJeuneFille(),
          "Né(e) le"=>$contact->getMembreConjoint()->getDateNaissance()?$contact->getMembreConjoint()->getDateNaissance()->format('d/m/Y'):'',
          ""=>''
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

      $repository = $this->getDoctrine()->getRepository('AppBundle:Parametre');
      $paramDroitEntree = $repository->findOneBy(array('code'=>'DROIT_ENTREE'));
      $paramAnnuel = $repository->findOneBy(array('code'=>'COTISATION_MONTANT_ANNUEL'));
      $paramSemestriel = $repository->findOneBy(array('code'=>'COTISATION_MONTANT_SEMESTRIEL'));
      $paramDossier = $repository->findOneBy(array('code'=>'FRAIS_DOSSIER'));

      $pdf->CotisationBlock(array(
        'annee'=>$datetime->format('Y'),
        'droit_entree'=>$paramDroitEntree->getValue(),
        'cotisation_annuelle'=>$paramAnnuel->getValue(),
        'cotisation_semestrielle'=>$paramSemestriel->getValue(),
        'ouverture_dossier'=>$paramDossier->getValue(),
        ));
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
      $pdf->AddParagraphe('Cher(e) Membre,');
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

    /**
     * @Route("pdf/contact/{idContact}/generer/lettre-section/{target}", name="generate_lettre_section", requirements={"target":"Président|Secrétaire|Trésorier"})
     * @Security("has_role('ROLE_USER')")
     */
    public function generateLettreSectionAction($idContact,$target)
    {

      $date = New \DateTime();

      $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($idContact);

      $pdf = new PDF_DefaultModel();
      $pdf->AddPage();
      $pdf->Title('LETTRE SECTION');
      $pdf->RightText("À l'attention du ".$target." de la section\nStrasbourg,\nle ".$date->format('d/m')."\nSection : ".$contact->getSection()->getNom());
      $pdf->SetLeftMargin(40);
      $pdf->AddParagraphe('Cher(e) Membre,');
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

    /**
     * @Route("pdf/contact/{idContact}/generer/invitation-ag/{target}", name="generate_invitation_ag", requirements={"target":"Président|Vice-Président|Secrétaire|Trésorier"})
     * @Security("has_role('ROLE_USER')")
     */
    public function generateInvitationAGAction($idContact,$target)
    {

      $date = New \DateTime();

      $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($idContact);

      $pdf = new PDF_DefaultModel();
      $pdf->AddPage();
      $pdf->RightText(
        ($contact->getCivilite()?$contact->getCivilite()->getLabel():"Madame/Monsieur")." ".$contact->getNom()." ".$contact->getPrenom().
        "\n".$contact->getAdresse().
        ($contact->getAdresseComp()?"\n".$contact->getAdresseComp():'').
        "\n".$contact->getCp()." ".$contact->getCommune());
      $pdf->SetLeftMargin(40);
      $pdf->SetFontDefault();
      $pdf->AddParagraphe('Cher(e) Membre,',true);
      $pdf->AddParagraphe('Nous tenons à vous remercier pour votre fidélité à notre section locale et à la grande famille de l\'UNIAT-ALSACE. C\'est pourquoi le Comité de votre section a décidé de vous décerner le diplôme et l\'insigne d\'honneur de l\'UNIAT. Nous serons particulièrement heureux de pouvoir vous les remettre lors de l\'assemblée générale, qui se tiendra le :');
      $nextAg = $this->getDoctrine()
        ->getRepository('AppBundle:AssembleeGenerale')
        ->findNextBySection($contact->getSection());
      
      $pdf->Ln(5);
      if($nextAg){
        $pdf->Title($nextAg->getDate()->format('d/m/Y').' à '.$nextAg->getLieu());
      }else{
        $pdf->Title('');
      }

      $pdf->AddParagraphe('Nous espérons que vous pourrez être parmi nous à cette occasion et vous remercions de nous retourner le talon ci-dessous.',true);
      $pdf->AddParagraphe('Recevez, Chere(e) Membre, nos cordiales salutations.',true);

      $pdf->Signature('','Le '.$target.' : ');

      $pdf->LigneDecoupe('TALON - REPONSE');
      $pdf->Ln(5);

      $pdf->AddComponent('UNIAT',strtoupper($contact->getSection()->getNom()),'text','1/2');
      $pdf->AddComponent('Assemblée du :',$nextAg?$nextAg->getDate()->format('d/m/Y'):'','text','1/2');
      $pdf->Ln();
      $pdf->SetLeftMargin(40);
      $pdf->AddComponent('Nom, prénom',$contact->getNom().' '.$contact->getPrenom(),'text','1/2');
      $pdf->Ln();
      $pdf->SetLeftMargin(40);
      $pdf->AddComponent('J\'assisterai à l\'assemblée générale','','cb','1');
      $pdf->Ln();
      $pdf->SetLeftMargin(40);
      $pdf->AddComponent('Je n\'assisterai pas à l\'assemblée générale','','cb','1');

      $pdf->SetY($pdf->GetY()-23);
      $pdf->SetLeftMargin($pdf->GetPageWidth()-75);
      $pdf->Signature('Date : ','Signature : ');

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
     * @Route("pdf/contact/{idContact}/generer/lettre-accompagnement", name="generate_lettre_accompagnement")
     * @Security("has_role('ROLE_USER')")
     */
    public function generateLettreAccompagnementAction($idContact)
    {

      $date = New \DateTime();

      $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($idContact);

      $pdf = new PDF_DefaultModel();
      $pdf->AddPage();
      $pdf->Title('LETTRE D\'ACCOMPAGNEMENT');
      $pdf->RightText("Strasbourg,\nle ".$date->format('d/m')."\nSection : ".$contact->getSection()->getNom());
      $pdf->SetLeftMargin(40);
      $pdf->AddParagraphe('Cher(e) Membre,');
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

    /**
     * @Route("pdf/contact/{idContact}/generer/carte-id-fonction", name="generate_carte_id_fonction")
     * @Security("has_role('ROLE_USER')")
     */
    public function generateCarteIDFonctionAction($idContact)
    {

      $date = New \DateTime();

      $contact = $this->getDoctrine()
              ->getRepository('AppBundle:Contact')
              ->find($idContact);

      $fonction = 'Aucune';
      if($contact->getFonctionSection()){
        $fonction = $contact->getFonctionSection()->getLabel();
      }else{
        if ($contact->getStatutJuridique()) {
          $fonction = $contact->getStatutJuridique()->getLabel();
        }
      }

      $pdf = new PDF_CarteIDFonction();
      $pdf->AddPage();
      $pdf->Title('UNIAT - CARTE D\'IDENTITÉ DE FONCTION');
      
      //col 1
      $pdf->SetLeftMargin(15);
      $svgY = $pdf->GetY();
      $pdf->AddPrimaryLine('SECTION LOCALE DE : ',$contact->getSection()->getNom());
      $pdf->AddPrimaryLine('FONCTION : ',$fonction);
      $pdf->PhotoHolder();

      //col 2
      $pdf->SetY($svgY+3);
      $pdf->SetLeftMargin(110);
      $pdf->AddDefaultLine('Nom : ',$contact->getNom());
      $pdf->AddDefaultLine('Prénom : ',$contact->getPrenom());
      $pdf->AddDefaultLine('Né(e) le : ',$contact->getDateNaissance()->format('d/m/Y'));
      $pdf->AddDefaultLine('Adresse : ',$contact->getAdresse());
      if($contact->getAdresseComp()&&$contact->getAdresseComp()!=''){
        $pdf->AddDefaultLine('',$contact->getAdresseComp());
      }
      $pdf->AddDefaultLine('',$contact->getCp().' - '.$contact->getCommune());
      $pdf->AddDefaultLine('Strasbourg, le : ',$date->format('d/m/Y'));
      $pdf->AddDefaultLine('Pour le Groupement Alsace,','');
      $pdf->Signature('','Le président : ');

      $pdf->Separator();
      
      $pdf->SetLeftMargin(15);
      $pdf->Bottom();

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
     * @Route("/contact/{idContact}/generate-pieces", name="generate_pieces")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function generatePiecesAction($idContact, Request $request)
    {
        $contact = $this->getDoctrine()
          ->getRepository('AppBundle:Contact')
          ->find($idContact);

        $pieces = $request->request->get('cbDocs');
        $title = $request->request->get('txtTitle');
        $txtAdd = $request->request->get('txtAdd');

        $pdf = new PDF_DefaultModel();
        $pdf->AddPage();
        $pdf->Title('Fiche : '.$title);
        $pdf->setFontDefault();
        $pdf->SetFont('','',10);
        
        $pdf->AddParagraphe('Mme / M : <b>'.$contact->getNom().' '.$contact->getPrenom().'</b><br />Section : <b>'.($contact->getSection()?$contact->getSection()->getNom():'').'</b>');
        
        $pdf->Ln(10);
        $pdf->SetFont('','B');
        $pdf->AddParagraphe($request->request->get('txtFullTitle'));
        $pdf->Ln(10);

        $pdf->Listing('Pièces à fournir',$pieces);
        
        if($txtAdd!=''){
          $pdf->SetFont('','',10);
          $pdf->AddParagraphe('<b>Informations complémentaires</b> : <br />'.$txtAdd);
        }

        $pdf->SetY($pdf->GetPageHeight()-65);
        $pdf->SetLeftMargin(120);
        $pdf->Signature('Date :','Le délégué :');

        $response = new Response();
        $response->setContent($pdf->Output());

        $response->headers->set(
           'Content-Type',
           'application/pdf'
        );

        return $response; 
    }

    /**
     * @Route("/contact/{idContact}/generate-divers-rens", name="generate_divers_rens")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function generateDiversRensAction($idContact, Request $request)
    {
        $contact = $this->getDoctrine()
          ->getRepository('AppBundle:Contact')
          ->find($idContact);

        $title = 'Divers Renseignements';
        $txtObjet = $request->request->get('txtObjet');
        $txtPieces = $request->request->get('txtPieces');
        $txtRenseignements = $request->request->get('txtRenseignements');
        $txtFormalites = $request->request->get('txtFormalites');


        $pdf = new PDF_DefaultModel();
        $pdf->AddPage();
        $pdf->Title('Fiche : '.$title);
        $pdf->setFontDefault();
        $pdf->SetFont('','',10);
        
        $pdf->AddParagraphe('Mme / M : <b>'.$contact->getNom().' '.$contact->getPrenom().'</b><br />Section : <b>'.($contact->getSection()?$contact->getSection()->getNom():'').'</b>');
        
        $pdf->AddParagraphe('<b>Objet</b><br />'.$txtObjet);
        $pdf->AddParagraphe('<b>Pièces à fournir</b><br />'.$txtPieces);
        $pdf->AddParagraphe('<b>Renseignements à donner</b><br />'.$txtRenseignements);
        $pdf->AddParagraphe('<b>Formalités à accomplir</b><br />'.$txtFormalites);


        $pdf->SetY($pdf->GetPageHeight()-65);
        $pdf->SetLeftMargin(120);
        $pdf->Signature('Date :','Le délégué :');

        $response = new Response();
        $response->setContent($pdf->Output());

        $response->headers->set(
           'Content-Type',
           'application/pdf'
        );

        return $response; 
    }

}
