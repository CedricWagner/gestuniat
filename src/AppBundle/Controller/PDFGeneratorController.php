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
use AppBundle\Utils\FPDF\templates\Table as PDF_Table;
use AppBundle\Utils\FPDF\templates\OrdreMission as PDF_OrdreMission;


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
     * @Route("pdf/contact/{idPermanence}/generer/ordre-permanence/{target}", name="generate_ordre_perm", requirements={"target":"Président|Vice-Président|Secrétaire|Trésorier"})
     * @Security("has_role('ROLE_USER')")
     */
    public function generateOrdrePermAction($idPermanence,$target)
    {

      $date = New \DateTime();

      $perm = $this->getDoctrine()
              ->getRepository('AppBundle:Permanence')
              ->find($idPermanence);

      $fonctionSection = $this->getDoctrine()
        ->getRepository('AppBundle:FonctionSection')
        ->findOneBy(array('label'=>$target));

      if(!$fonctionSection){
        $this->get('session')->getFlashBag()->add('danger', 'Génération impossible : la fonction "'.$target.'"" n\'existe pas');
        return $this->redirectToRoute('list_permanences',['idSection'=>$perm->getSection()->getId()]);
      }else{
        $contact = $this->getDoctrine()
          ->getRepository('AppBundle:Contact')
          ->findOneBy(array('section'=>$perm->getSection(),'fonctionSection'=>$fonctionSection));

        if(!$contact){
          $this->get('session')->getFlashBag()->add('danger', 'Génération impossible : aucun contact n\'occupe la fonction '.$target);
          return $this->redirectToRoute('list_permanences',['idSection'=>$perm->getSection()->getId()]);
        }
      }


      //effectifs and patrimoines
      $effectifs = array();
      $patrimoines = array();
      $years = range($date->format('Y')-6, $date->format('Y'));
      foreach ($years as $year) {
        $effectif = $this->getDoctrine()
          ->getRepository('AppBundle:Effectif')
          ->findOneBy(array('annee'=>$year,'section'=>$perm->getSection()));
        if($effectif){
          $effectifs[$year] = $effectif->getValeur();
        }else{
          $effectifs[$year] = '-';
        }

        $patrimoine = $this->getDoctrine()
          ->getRepository('AppBundle:Patrimoine')
          ->findOneBy(array('annee'=>$year,'section'=>$perm->getSection()));
        if($patrimoine){
          $patrimoines[$year] = $patrimoine->getValeur();
        }else{
          $patrimoines[$year] = '-';
        }
      }

      //AG
      $lastAG = $this->getDoctrine()
        ->getRepository('AppBundle:AssembleeGenerale')
        ->findOneBy(array('section'=>$perm->getSection()),array('date'=>'DESC'));

      //Dest rentiers
      $destRentiers = $this->getDoctrine()
        ->getRepository('AppBundle:Contact')
        ->findBy(array('section'=>$perm->getSection(),'isRentier'=>true));
      $txtDests = ''; 
      foreach ($destRentiers as $dest) {
        $txtDests .= $dest->getNom().' '.$dest->getPrenom().'      ';
      }
      $nbRentiers = $this->getDoctrine()
        ->getRepository('AppBundle:Contact')
        ->countContactsBySection($perm->getSection());

      $pdf = new PDF_DefaultModel();
      $pdf->AddPage();
      $pdf->Title('ORDRE DE PERMANENCE - '.$perm->getSection()->getNom());
      $pdf->SetLeftMargin(40);
      $pdf->AddParagraphe('<b>Date de permanence :</b>',true);
      $pdf->AddParagraphe('<b>Chargé de mission :</b>');
      $pdf->AddParagraphe('Arrivée :                         Départ :                          Insertion presse : ');
      $pdf->AddParagraphe('Militants présents :');
      $pdf->AddParagraphe('Personnes présentes :');
      $pdf->Ln(10);
      $pdf->AddParagraphe('Dons :');
      $pdf->AddParagraphe('<b>Contact :</b>');
      $pdf->InlineMultiCell(20,$contact->getNom().' '.$contact->getPrenom(),true);
      $pdf->InlineMultiCell(20,$fonctionSection->getLabel());
      $pdf->InlineMultiCell(40,$contact->getAdresse());
      $pdf->InlineMultiCell(15,$contact->getCp());
      $pdf->InlineMultiCell(25,$contact->getCommune());
      $pdf->InlineMultiCell(25,$contact->getTelFixe()!=''?$contact->getTelFixe():$contact->getTelPort());
      $pdf->Ln(5);
      $pdf->SetLeftMargin(40);
      $pdf->AddParagraphe('<b>Subventions :</b> '.$perm->getSection()->getSubventions());
      $pdf->AddParagraphe('<b>Effectifs : </b>');
      $pdf->BorderedCells($effectifs);
      $pdf->Ln(5);
      $pdf->AddParagraphe('<b>Patrimoines : </b>');
      $pdf->BorderedCells($patrimoines);
      $pdf->Ln(5);
      $pdf->AddParagraphe('<b>Permanence Siège : </b>');
      $pdf->InlineMultiCell(50,$perm->getPeriodicite()->getLabel(),true);
      $pdf->InlineMultiCell(50,$perm->getHoraire());
      $pdf->InlineMultiCell(50,$perm->getLieu());
      $pdf->Ln();
      $pdf->SetLeftMargin(40);
      $pdf->AddParagraphe('<b>Assemblée générale : </b>'.($lastAG ? $lastAG->getDate()->format('d/m/Y') : ''));
      $pdf->AddParagraphe('<b>Destinataire(s) Rentier : </b>'.$txtDests);
      $pdf->AddParagraphe('<b>Nombre de Rentiers : </b>'.$nbRentiers);
      $pdf->AddParagraphe('<b>Autres informations : </b>');

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

    /**
     * @Route("/don/{idDon}/generate-recu-don", name="generate_recu_don")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function generateRecuDonAction($idDon)
    {
        $don = $this->getDoctrine()
          ->getRepository('AppBundle:Don')
          ->find($idDon);

        $pNom = $this->getDoctrine()->getRepository('AppBundle:Parametre')->findOneBy(array('code'=>'NOM'));
        $pAdresse = $this->getDoctrine()->getRepository('AppBundle:Parametre')->findOneBy(array('code'=>'ADRESSE_SIEGE'));
        $pDecret = $this->getDoctrine()->getRepository('AppBundle:Parametre')->findOneBy(array('code'=>'DECRET'));



        $pdf = new PDF_Table();
        $pdf->AddPage();
        $pdf->Title(strtoupper('RECU - ASSOCIATION'));
        $pdf->setFontDefault();
        $pdf->SetFont('','',10);
        $pdf->EnteteCerfa('N° 11580*03','ARTICLE 238 bis - 5 CODE GÉNÉRAL DES IMPÔTS');
        $pdf->TableLine('NOM DE L\'ENTREPRISE :',$pNom->getValue());
        $pdf->TableLine('ADRESSE DU SIÈGE :',$pAdresse->getValue());
        $pdf->TableLine('DÉCRET :',$pDecret->getValue());
        $pdf->TableLine('NOM DU DONATEUR :',$don->getContact()->getPrenom().' '.strtoupper($don->getContact()->getNom()));
        $pdf->TableLine('ADRESSE DU DONATEUR :',$don->getContact()->getAdresse().' \n'.$don->getContact()->getCp().' '.strtoupper($don->getContact()->getCommune()));
        $pdf->TableLine("L'association reconnaît avoir reçu à titre \nde don, la somme de :", $don->getMontant().' euros','1/2');
        $pdf->TableLine("Somme en toutes lettres", strtoupper($this->get('app.tools')->asLetters($don->getMontant(),true)),'1/2');
        $pdf->specTableLine('Date de paiement : '.$don->getDate()->format('d/m/Y'),'Mode de versement : '.($don->getMoyenPaiement()?$don->getMoyenPaiement()->getLabel():''));
        $pdf->SetFont('','',8);
        $pdf->AddParagraphe("<i>Le don n'ouvre droit à déduction que dans la mesure
où les conditions générales prévues à l'article 238 bis-1
du Code général des impôts sont remplies.
C'est-à-dire s'il est effectué « au profit d'oeuvres ou
d'organismes d'intérêt général, de caractère
philantropique, éducatif, scientifique, social, familial
ou culturel ».</i>");

        $response = new Response();
        $response->setContent($pdf->Output());

        $response->headers->set(
           'Content-Type',
           'application/pdf'
        );

        return $response; 
    }

    /**
     * @Route("/don/{idDon}/generate-remerciement-don", name="generate_remerciement_don")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function generateRemDonAction($idDon)
    {
        $don = $this->getDoctrine()
          ->getRepository('AppBundle:Don')
          ->find($idDon);

        $date = new \DateTime();

        $pdf = new PDF_DefaultModel();
        $pdf->AddPage();
        $pdf->Title(strtoupper('LETTRE DE REMERCIEMENT'));
        $pdf->setFontDefault();
        $pdf->SetFont('','',10);
        $pdf->RightText("Strasbourg \nle ".$date->format('d/m/Y')." \nSection : ".($don->getContact()->getSection()?$don->getContact()->getSection()->getNom():''));
        $pdf->AddParagraphe($this->render('docs/lettres/remerciement-don.html.twig',['montant'=>$don->getMontant()])->getContent());

        $response = new Response();
        $response->setContent($pdf->Output());

        $response->headers->set(
           'Content-Type',
           'application/pdf'
        );

        return $response; 
    }

    /**
     * @Route("/assemblee-generale/{idAG}/generer-ordre-de-mission", name="generate_ordre_mission")
     * @Security("has_role('ROLE_SPECTATOR')")
     */
    public function generateOrdreMissionAction($idAG, Request $request)
    {
        $ag = $this->getDoctrine()
          ->getRepository('AppBundle:AssembleeGenerale')
          ->find($idAG);

        $date = new \DateTime();

        $dateDip = $request->request->get('txtDate');
        $txtAutresInfos = $request->request->get('txtAutresInfos');


        $diplomes = $this->getDoctrine()
          ->getRepository('AppBundle:ContactDiplome')
          ->findLastDiplomes($ag->getSection(),new \DateTime($dateDip));

        $lstDip = array();
        foreach ($diplomes as $contactDiplome) {
          $lstDip[$contactDiplome->getDiplome()->getLabel()][] = $contactDiplome; 
        }

        $txtDip = '';
        foreach ($lstDip as $key => $lst) {
          $txtDip.= sizeof($lst).' '.$key.'  ';
        }

        $currentPat = $this->getDoctrine()
          ->getRepository('AppBundle:Patrimoine')
          ->findOneBy(array('section'=>$ag->getSection(),'annee'=>$ag->getDate()->format('Y'))); 

        $prevPat = $this->getDoctrine()
          ->getRepository('AppBundle:Patrimoine')
          ->findOneBy(array('section'=>$ag->getSection(),'annee'=>$ag->getDate()->format('Y')-1)); 

        $pdf = new PDF_OrdreMission();
        $pdf->AddPage();
        $pdf->Title(strtoupper('ASSEMBLÉE GÉNÉRALE - ORDRE DE MISSION'));
        $pdf->setFontDefault();
        $pdf->SetFont('','',10);
        $pdf->AddParagraphe('Orateur : '.$ag->getOrateur());
        $pdf->AddParagraphe('Objet : <b>Assemblée générale de la section locale de '.$ag->getSection()->getNom().'</b>');
        $pdf->AddParagraphe('Jour : '.$ag->getDate()->format('d/m/Y'));
        $pdf->SetY($pdf->GetY()-10);
        $pdf->SetLeftMargin(115);
        $pdf->AddParagraphe('Heure : '.$ag->getHeure());
        $pdf->SetLeftMargin(40);
        $pdf->AddParagraphe('Lieu : '.$ag->getLieu());

        $pdf->Ln(10);
        $pdf->Separator();
        
        $svgY = $pdf->GetY();
        $pdf->AddParagraphe('<b>COMPTE RENDU</b> :');
        $pdf->AddParagraphe('Réunion annoncée dans la presse ?');
        $pdf->Ln(3);
        $pdf->AddParagraphe('Des affiches ont été apposées ?');

        $pdf->SetleftMargin(140);
        $pdf->SetY($svgY);
        $pdf->AddParagraphe('<b>NOMBRE DE MEMBRES </b>:');
        $pdf->AddParagraphe('- exercice '.($ag->getDate()->format('Y')-1).' : ');
        $pdf->Ln(3);
        $pdf->AddParagraphe('- exercice '.($ag->getDate()->format('Y')).' : ');

        $pdf->SetleftMargin(40);
        $pdf->Ln(3);
        $pdf->AddParagraphe('Nombre de personnes présentes ?');
        $pdf->Ln(3);
        $pdf->AddParagraphe('Diplômes : '.$txtDip);
        $pdf->Ln(3);
        $pdf->AddParagraphe('Autres informations utiles : '.$txtAutresInfos);
        $pdf->Ln(20);
        $pdf->Separator();

        $pdf->SetFontRed();
        $pdf->AddParagraphe('<b>IMPORTANT : Cet ordre de mission est à renvoyer dans un délai de 8 jours après l\'Assemblée au Secrétariat.</b>');
        $pdf->SetFontDefault();
        $pdf->AddParagraphe('Pour le remboursement des frais, merci de remplir la fiche de frais prévu à cet effet et la retourner au secrétariat');
        $pdf->AddParagraphe('Date :');
        $pdf->SetleftMargin(142);
        $pdf->SetY($pdf->GetY()-20);
        $pdf->Signature('','Signature du délégué :');

        $pdf->SetFont('','',8);
        $pdf->SetY($pdf->GetY()-20);
        $pdf->SetleftMargin(12);
        $pdf->AddParagraphe('<i>Association régionale groupant les Assurés Sociaux, Invalides, Accidentés du travail, Veuves et Retraités, créée en 1924, inscrite au Registre  des Associations, sous VOL. XIX N°12 du Tribunal d’Instance de Strasbourg. Associée à la F.N.A.R. et à la F.N.A.T.H.</i>');

        $pdf->addHeader = false;
        $pdf->AddPage();
        $pdf->SetFont('','',10);
        $pdf->AddParagraphe('<b>RAPPORT D\'ACTIVITÉ </b>:');
        $pdf->AddParagraphe('Présenté par : ');
        $pdf->AddParagraphe('Excursions : ');
        $pdf->AddParagraphe('Observations : ');
        $pdf->AddParagraphe('<b>RAPPORT FINANCIER</b> :        Présenté par : ');
        $pdf->AddParagraphe('                                                Pour la période du :                                       au : ');
        $pdf->SetFontRed();
        $pdf->AddParagraphe('<b>Cette rubrique doit impérativement être complétée, soit lors de l\'AG soit après entretien avec le Trésorier :</b>');
        $pdf->SetFontDefault();
        $pdf->RedSection(array(
            1 => array(
                'label'=>'Recettes totales : ',
                'size'=>50,
              ),
            2 => array(
                'label'=>'Euros, soit : ',
                'size'=>60,
              ),
          ));
        $pdf->Ln(10);
        $pdf->SetFont('','',10);
        $pdf->Cell(50,10,'Cotisation : ');
        $pdf->Cell(50,10,'Sub. / Dons : ');
        $pdf->Cell(50,10,'Loisirs : ');
        $pdf->Cell(50,10,'Divers : ');
        $pdf->Ln();
        $pdf->RedSection(array(
            1 => array(
                'label'=>'Dépenses totales :',
                'size'=>50,
              ),
            2 => array(
                'label'=>'Euros, soit : ',
                'size'=>60,
              ),
          ));
        $pdf->Ln(10);
        $pdf->SetFont('','',10);
        $pdf->Cell(50,10,'Cotisation : ');
        $pdf->Cell(50,10,'Frais visite dom. : ');
        $pdf->Cell(50,10,'Fonct : ');
        $pdf->Cell(50,10,'Frais statut : ');
        $pdf->Ln();
        $pdf->Cell(50,10,'Loisirs : ');
        $pdf->Cell(50,10,'Divers : ');
        $pdf->Ln();
        $pdf->RedSection(array(
            1 => array(
                'label'=>'Résultat :        ',
                'size'=>50,
              ),
            2 => array(
                'label'=>'Bénéfice : ',
                'size'=>10,
              ),
            3 => array(
                'label'=>'Déficit : ',
                'size'=>10,
              ),
          ));
        $pdf->Ln(10);
        $pdf->SetFont('','B',10);
        $pdf->Cell(60,10,'Patrimoine au 01/01/'.$ag->getDate()->format('Y').' : '.($currentPat?$currentPat->getValeur().' '.chr(128):''));
        $pdf->Cell(45,10,utf8_decode('Intérêt épargne : ').($currentPat?$currentPat->getInterets().' '.chr(128):''));
        $pdf->Cell(60,10,'Patrimoine au  : 01/01/'.($ag->getDate()->format('Y')-1).' : '.($prevPat?$prevPat->getValeur().' '.chr(128):''));
        $pdf->Ln(5);
        $pdf->SetFont('','',10);
        $pdf->AddParagraphe('<b>COMPOSITION DU NOUVAU COMITÉ : </b>');
        $pdf->AddParagraphe('Président : ');
        $pdf->AddParagraphe('Vice-Président : ');
        $pdf->AddParagraphe('Secrétaire : ');
        $pdf->AddParagraphe('Trésorier : ');
        $pdf->AddParagraphe('Assesseurs : ');
        $pdf->AddParagraphe('Encaisseurs : ');
        $pdf->AddParagraphe('Rév. aux comptes : ');
        $pdf->AddParagraphe('<b>NOTES SUR LE DÉROULEMENT / APPRÉCIATIONS / SOUHAITS EXPRIMÉS PAR LA SECTION : </b>');


        $response = new Response();
        $response->setContent($pdf->Output());

        $response->headers->set(
           'Content-Type',
           'application/pdf'
        );

        return $response; 
    }

}
