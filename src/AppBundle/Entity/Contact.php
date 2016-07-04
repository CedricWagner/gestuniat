<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Debug\Exception\ContextErrorException as ContextErrorException;

/**
 * Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactRepository")
 */
class Contact
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=100)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     */
    private $nom;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="prenom", type="string", length=100)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=100, nullable=true)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="telFixe", type="string", length=20, nullable=true)
     */
    private $telFixe;

    /**
     * @var string
     *
     * @ORM\Column(name="telPort", type="string", length=20, nullable=true)
     */
    private $telPort;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseComp", type="string", length=255, nullable=true)
     */
    private $adresseComp;

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=10, nullable=true)
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="commune", type="string", length=155, nullable=true)
     */
    private $commune;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=50, nullable=true)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="bp", type="string", length=100, nullable=true)
     */
    private $bp;

    /**
     * @var string
     *
     * @ORM\Column(name="dateNaissance", type="date", nullable=true)
     */
    private $dateNaissance;
    
    /**
     * @var string
     *
     * @ORM\Column(name="lieuNaissance", type="string", length=255, nullable=true)
     */
    private $lieuNaissance;

    /**
     * @var bool
     *
     * @ORM\Column(name="isRentier", type="boolean")
     */
    private $isRentier;

    /**
     * @var bool
     *
     * @ORM\Column(name="nbRentiers", type="integer", nullable=true)
     */
    private $nbRentiers;

    /**
     * @var bool
     *
     * @ORM\Column(name="isBI", type="boolean")
     */
    private $isBI;

    /**
     * @var bool
     *
     * @ORM\Column(name="isCourrier", type="boolean")
     */
    private $isCourrier;

    /**
     * @var bool
     *
     * @ORM\Column(name="isEnvoiIndiv", type="boolean")
     */
    private $isEnvoiIndiv;

    /**
     * @var bool
     *
     * @ORM\Column(name="isDossierPaye", type="boolean")
     */
    private $isDossierPaye;

    /**
     * @var string
     *
     * @ORM\Column(name="encaisseur", type="string", length=255, nullable=true)
     */
    private $encaisseur;

    /**
     * @var string
     *
     * @ORM\Column(name="observation", type="text", nullable=true)
     */
    private $observation;

    /**
     * @var int
     *
     * @ORM\Column(name="numAdh", type="integer")
     */
    private $numAdh;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEntree", type="date", nullable=true)
     */
    private $dateEntree;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateSortie", type="date", nullable=true)
     */
    private $dateSortie;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDelivranceFonc", type="date", nullable=true)
     */
    private $dateDelivranceFonc;

    /**
     * @var string
     *
     * @ORM\Column(name="fonctionRepresentation", type="string", length=155, nullable=true)
     */
    private $fonctionRepresentation;

    /**
     * @var bool
     *
     * @ORM\Column(name="isCA", type="boolean")
     */
    private $isCA;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDeces", type="date", nullable=true)
     */
    private $dateDeces;

    /**
     * @var string
     *
     * @ORM\Column(name="mentionDeces", type="text", nullable=true)
     */
    private $mentionDeces;

    /**
     * @var bool
     *
     * @ORM\Column(name="isOffreDecouverte", type="boolean")
     */
    private $isOffreDecouverte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateOffreDecouverte", type="date", nullable=true)
     */
    private $dateOffreDecouverte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdhesion", type="date", nullable=true)
     */
    private $dateAdhesion;

    /**
     * @var string
     *
     * @ORM\Column(name="typeAdhesion", type="string", length=20, nullable=true)
     */
    private $typeAdhesion;

    /**
     * @var string
     *
     * @ORM\Column(name="nomJeuneFille", type="string", length=100, nullable=true)
     */
    private $nomJeuneFille;

    /**
     * @var string
     *
     * @ORM\Column(name="numSecu", type="string", length=100, nullable=true)
     */
    private $numSecu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCIF", type="date", nullable=true)
     */
    private $dateCIF;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="isActif", type="boolean")
     */
    private $isActif;

    /**
     * @ORM\ManyToOne(targetEntity="StatutJuridique")
     * @ORM\JoinColumn(name="statutJuridique_id", referencedColumnName="id")
     */
    protected $statutJuridique;

    /**
     * @ORM\ManyToOne(targetEntity="FonctionSection")
     * @ORM\JoinColumn(name="fonctionSection_id", referencedColumnName="id")
     */
    protected $fonctionSection;
    
    /**
     * @ORM\ManyToOne(targetEntity="FonctionGroupement")
     * @ORM\JoinColumn(name="fonctionGroupement_id", referencedColumnName="id")
     */
    protected $fonctionGroupement;

    /**
     * @ORM\ManyToOne(targetEntity="StatutMatrimonial")
     * @ORM\JoinColumn(name="statutMatrimonial_id", referencedColumnName="id")
     */
    protected $statutMatrimonial;

    /**
     * @ORM\ManyToOne(targetEntity="Civilite")
     * @ORM\JoinColumn(name="civilite_id", referencedColumnName="id")
     */
    protected $civilite;

    /**
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumn(name="membreConjoint_id", referencedColumnName="id")
     */
    protected $membreConjoint;

    /**
     * @ORM\ManyToOne(targetEntity="Section")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    protected $section;

    /**
     * @ORM\OneToMany(targetEntity="Cotisation", mappedBy="contact")
     */
    protected $cotisations;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Contact
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Contact
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return Contact
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set telFixe
     *
     * @param string $telFixe
     *
     * @return Contact
     */
    public function setTelFixe($telFixe)
    {
        $this->telFixe = $telFixe;

        return $this;
    }

    /**
     * Get telFixe
     *
     * @return string
     */
    public function getTelFixe()
    {
        return $this->telFixe;
    }

    /**
     * Set telPort
     *
     * @param string $telPort
     *
     * @return Contact
     */
    public function setTelPort($telPort)
    {
        $this->telPort = $telPort;

        return $this;
    }

    /**
     * Get telPort
     *
     * @return string
     */
    public function getTelPort()
    {
        return $this->telPort;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Contact
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set adresseComp
     *
     * @param string $adresseComp
     *
     * @return Contact
     */
    public function setAdresseComp($adresseComp)
    {
        $this->adresseComp = $adresseComp;

        return $this;
    }

    /**
     * Get adresseComp
     *
     * @return string
     */
    public function getAdresseComp()
    {
        return $this->adresseComp;
    }

    /**
     * Set cp
     *
     * @param string $cp
     *
     * @return Contact
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }


    /**
     * Set commune
     *
     * @param string $commune
     *
     * @return Contact
     */
    public function setCommune($commune)
    {
        $this->commune = $commune;

        return $this;
    }

    /**
     * Get commune
     *
     * @return string
     */
    public function getCommune()
    {
        return $this->commune;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Contact
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set bp
     *
     * @param string $bp
     *
     * @return Contact
     */
    public function setBp($bp)
    {
        $this->bp = $bp;

        return $this;
    }

    /**
     * Get bp
     *
     * @return string
     */
    public function getBp()
    {
        return $this->bp;
    }

    /**
     * Set isRentier
     *
     * @param boolean $isRentier
     *
     * @return Contact
     */
    public function setIsRentier($isRentier)
    {
        $this->isRentier = $isRentier;

        return $this;
    }

    /**
     * Get isRentier
     *
     * @return bool
     */
    public function getIsRentier()
    {
        return $this->isRentier;
    }

    /**
     * Set isBI
     *
     * @param boolean $isBI
     *
     * @return Contact
     */
    public function setIsBI($isBI)
    {
        $this->isBI = $isBI;

        return $this;
    }

    /**
     * Get isBI
     *
     * @return bool
     */
    public function getIsBI()
    {
        return $this->isBI;
    }

    /**
     * Set isCourrier
     *
     * @param boolean $isCourrier
     *
     * @return Contact
     */
    public function setIsCourrier($isCourrier)
    {
        $this->isCourrier = $isCourrier;

        return $this;
    }

    /**
     * Get isCourrier
     *
     * @return bool
     */
    public function getIsCourrier()
    {
        return $this->isCourrier;
    }

    /**
     * Set isEnvoiIndiv
     *
     * @param boolean $isEnvoiIndiv
     *
     * @return Contact
     */
    public function setIsEnvoiIndiv($isEnvoiIndiv)
    {
        $this->isEnvoiIndiv = $isEnvoiIndiv;

        return $this;
    }

    /**
     * Get isEnvoiIndiv
     *
     * @return bool
     */
    public function getIsEnvoiIndiv()
    {
        return $this->isEnvoiIndiv;
    }

    /**
     * Set isDossierPaye
     *
     * @param boolean $isDossierPaye
     *
     * @return Contact
     */
    public function setIsDossierPaye($isDossierPaye)
    {
        $this->isDossierPaye = $isDossierPaye;

        return $this;
    }

    /**
     * Get isDossierPaye
     *
     * @return bool
     */
    public function getIsDossierPaye()
    {
        return $this->isDossierPaye;
    }

    /**
     * Set encaisseur
     *
     * @param string $encaisseur
     *
     * @return Contact
     */
    public function setEncaisseur($encaisseur)
    {
        $this->encaisseur = $encaisseur;

        return $this;
    }

    /**
     * Get encaisseur
     *
     * @return string
     */
    public function getEncaisseur()
    {
        return $this->encaisseur;
    }

    /**
     * Set observation
     *
     * @param string $observation
     *
     * @return Contact
     */
    public function setObservation($observation)
    {
        $this->observation = $observation;

        return $this;
    }

    /**
     * Get observation
     *
     * @return string
     */
    public function getObservation()
    {
        return $this->observation;
    }

    /**
     * Set numAdh
     *
     * @param integer $numAdh
     *
     * @return Contact
     */
    public function setNumAdh($numAdh)
    {
        $this->numAdh = $numAdh;

        return $this;
    }

    /**
     * Get numAdh
     *
     * @return int
     */
    public function getNumAdh()
    {
        return $this->numAdh;
    }

    /**
     * Set dateEntree
     *
     * @param \DateTime $dateEntree
     *
     * @return Contact
     */
    public function setDateEntree($dateEntree)
    {
        $this->dateEntree = $dateEntree;

        return $this;
    }

    /**
     * Get dateEntree
     *
     * @return \DateTime
     */
    public function getDateEntree()
    {
        return $this->dateEntree;
    }

    /**
     * Set dateSortie
     *
     * @param \DateTime $dateSortie
     *
     * @return Contact
     */
    public function setDateSortie($dateSortie)
    {
        $this->dateSortie = $dateSortie;

        return $this;
    }

    /**
     * Get dateSortie
     *
     * @return \DateTime
     */
    public function getDateSortie()
    {
        return $this->dateSortie;
    }

    /**
     * Set statutMat
     *
     * @param string $statutMat
     *
     * @return Contact
     */
    public function setStatutMat($statutMat)
    {
        $this->statutMat = $statutMat;

        return $this;
    }

    /**
     * Get statutMat
     *
     * @return string
     */
    public function getStatutMat()
    {
        return $this->statutMat;
    }

    /**
     * Set dateDelivranceFonc
     *
     * @param \DateTime $dateDelivranceFonc
     *
     * @return Contact
     */
    public function setDateDelivranceFonc($dateDelivranceFonc)
    {
        $this->dateDelivranceFonc = $dateDelivranceFonc;

        return $this;
    }

    /**
     * Get dateDelivranceFonc
     *
     * @return \DateTime
     */
    public function getDateDelivranceFonc()
    {
        return $this->dateDelivranceFonc;
    }

    /**
     * Set isCA
     *
     * @param boolean $isCA
     *
     * @return Contact
     */
    public function setIsCA($isCA)
    {
        $this->isCA = $isCA;

        return $this;
    }

    /**
     * Get isCA
     *
     * @return bool
     */
    public function getIsCA()
    {
        return $this->isCA;
    }

    /**
     * Set dateDeces
     *
     * @param \DateTime $dateDeces
     *
     * @return Contact
     */
    public function setDateDeces($dateDeces)
    {
        $this->dateDeces = $dateDeces;

        return $this;
    }

    /**
     * Get dateDeces
     *
     * @return \DateTime
     */
    public function getDateDeces()
    {
        return $this->dateDeces;
    }

    /**
     * Set mentionDeces
     *
     * @param string $mentionDeces
     *
     * @return Contact
     */
    public function setMentionDeces($mentionDeces)
    {
        $this->mentionDeces = $mentionDeces;

        return $this;
    }

    /**
     * Get mentionDeces
     *
     * @return string
     */
    public function getMentionDeces()
    {
        return $this->mentionDeces;
    }

    /**
     * Set isOffreDecouverte
     *
     * @param boolean $isOffreDecouverte
     *
     * @return Contact
     */
    public function setIsOffreDecouverte($isOffreDecouverte)
    {
        $this->isOffreDecouverte = $isOffreDecouverte;

        return $this;
    }

    /**
     * Get isOffreDecouverte
     *
     * @return bool
     */
    public function getIsOffreDecouverte()
    {
        return $this->isOffreDecouverte;
    }

    /**
     * Set dateOffreDecouverte
     *
     * @param \DateTime $dateOffreDecouverte
     *
     * @return Contact
     */
    public function setDateOffreDecouverte($dateOffreDecouverte)
    {
        $this->dateOffreDecouverte = $dateOffreDecouverte;

        return $this;
    }

    /**
     * Get dateOffreDecouverte
     *
     * @return \DateTime
     */
    public function getDateOffreDecouverte()
    {
        return $this->dateOffreDecouverte;
    }

    /**
     * Set dateAdhesion
     *
     * @param \DateTime $dateAdhesion
     *
     * @return Contact
     */
    public function setDateAdhesion($dateAdhesion)
    {
        $this->dateAdhesion = $dateAdhesion;

        return $this;
    }

    /**
     * Get dateAdhesion
     *
     * @return \DateTime
     */
    public function getDateAdhesion()
    {
        return $this->dateAdhesion;
    }

    /**
     * Set nomJeuneFille
     *
     * @param string $nomJeuneFille
     *
     * @return Contact
     */
    public function setNomJeuneFille($nomJeuneFille)
    {
        $this->nomJeuneFille = $nomJeuneFille;

        return $this;
    }

    /**
     * Get nomJeuneFille
     *
     * @return string
     */
    public function getNomJeuneFille()
    {
        return $this->nomJeuneFille;
    }

    /**
     * Set numSecu
     *
     * @param string $numSecu
     *
     * @return Contact
     */
    public function setNumSecu($numSecu)
    {
        $this->numSecu = $numSecu;

        return $this;
    }

    /**
     * Get numSecu
     *
     * @return string
     */
    public function getNumSecu()
    {
        return $this->numSecu;
    }

    /**
     * Set dateCIF
     *
     * @param \DateTime $dateCIF
     *
     * @return Contact
     */
    public function setDateCIF($dateCIF)
    {
        $this->dateCIF = $dateCIF;

        return $this;
    }

    /**
     * Get dateCIF
     *
     * @return \DateTime
     */
    public function getDateCIF()
    {
        return $this->dateCIF;
    }


    /**
     * Set statutJuridique
     *
     * @param \StatutJuridique $statutJuridique
     *
     * @return Contact
     */
    public function setStatutJuridique($statutJuridique)
    {
        $this->statutJuridique = $statutJuridique;

        return $this;
    }

    /**
     * Get statutJuridique
     *
     * @return \StatutJuridique
     */
    public function getStatutJuridique()
    {
        return $this->statutJuridique;
    }

    /**
     * Set fonctionSection
     *
     * @param \FonctionSection $fonctionSection
     *
     * @return Contact
     */
    public function setFonctionSection($fonctionSection)
    {
        $this->fonctionSection = $fonctionSection;

        return $this;
    }

    /**
     * Get fonctionSection
     *
     * @return \FonctionSection
     */
    public function getFonctionSection()
    {
        return $this->fonctionSection;
    }

    /**
     * Set fonctionGroupement
     *
     * @param \FonctionGroupement $fonctionGroupement
     *
     * @return Contact
     */
    public function setFonctionGroupement($fonctionGroupement)
    {
        $this->fonctionGroupement = $fonctionGroupement;

        return $this;
    }

    /**
     * Get fonctionGroupement
     *
     * @return \FonctionGroupement
     */
    public function getFonctionGroupement()
    {
        return $this->fonctionGroupement;
    }

    /**
     * Set fonctionRepresentation
     *
     * @param string $fonctionRepresentation
     *
     * @return Contact
     */
    public function setFonctionRepresentation($fonctionRepresentation)
    {
        $this->fonctionRepresentation = $fonctionRepresentation;

        return $this;
    }

    /**
     * Get fonctionRepresentation
     *
     * @return string
     */
    public function getFonctionRepresentation()
    {
        return $this->fonctionRepresentation;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Contact
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set lieuNaissance
     *
     * @param string $lieuNaissance
     *
     * @return Contact
     */
    public function setLieuNaissance($lieuNaissance)
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    /**
     * Get lieuNaissance
     *
     * @return string
     */
    public function getLieuNaissance()
    {
        return $this->lieuNaissance;
    }

    /**
     * Set statutMatrimonial
     *
     * @param \AppBundle\Entity\StatutMatrimonial $statutMatrimonial
     *
     * @return Contact
     */
    public function setStatutMatrimonial(\AppBundle\Entity\StatutMatrimonial $statutMatrimonial = null)
    {
        $this->statutMatrimonial = $statutMatrimonial;

        return $this;
    }

    /**
     * Get statutMatrimonial
     *
     * @return \AppBundle\Entity\StatutMatrimonial
     */
    public function getStatutMatrimonial()
    {
        return $this->statutMatrimonial;
    }

    /**
     * Set civilite
     *
     * @param \AppBundle\Entity\Civilite $civilite
     *
     * @return Contact
     */
    public function setCivilite(\AppBundle\Entity\Civilite $civilite = null)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get civilite
     *
     * @return \AppBundle\Entity\Civilite
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * Set membreConjoint
     *
     * @param \AppBundle\Entity\Contact $membreConjoint
     *
     * @return Contact
     */
    public function setMembreConjoint(\AppBundle\Entity\Contact $membreConjoint = null)
    {
        $this->membreConjoint = $membreConjoint;

        return $this;
    }

    /**
     * Get membreConjoint
     *
     * @return \AppBundle\Entity\Contact
     */
    public function getMembreConjoint()
    {
        return $this->membreConjoint;
    }

    /**
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return Contact
     */
    public function setSection(\AppBundle\Entity\Section $section = null)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return \AppBundle\Entity\Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set nbRentiers
     *
     * @param integer $nbRentiers
     *
     * @return Contact
     */
    public function setNbRentiers($nbRentiers)
    {
        $this->nbRentiers = $nbRentiers;

        return $this;
    }

    /**
     * Get nbRentiers
     *
     * @return integer
     */
    public function getNbRentiers()
    {
        return $this->nbRentiers;
    }

    /**
     * Set isActif
     *
     * @param boolean $isActif
     *
     * @return Contact
     */
    public function setIsActif($isActif)
    {
        $this->isActif = $isActif;

        return $this;
    }

    /**
     * Get isActif
     *
     * @return boolean
     */
    public function getIsActif()
    {
        return $this->isActif;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cotisations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add cotisation
     *
     * @param \AppBundle\Entity\Cotisation $cotisation
     *
     * @return Contact
     */
    public function addCotisation(\AppBundle\Entity\Cotisation $cotisation)
    {
        $this->cotisations[] = $cotisation;

        return $this;
    }

    /**
     * Remove cotisation
     *
     * @param \AppBundle\Entity\Cotisation $cotisation
     */
    public function removeCotisation(\AppBundle\Entity\Cotisation $cotisation)
    {
        $this->cotisations->removeElement($cotisation);
    }

    /**
     * Get cotisations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCotisations()
    {
        return $this->cotisations;
    }

    /**
     * Set typeAdhesion
     *
     * @param string $typeAdhesion
     *
     * @return Contact
     */
    public function setTypeAdhesion($typeAdhesion)
    {
        $this->typeAdhesion = $typeAdhesion;

        return $this;
    }

    /**
     * Get typeAdhesion
     *
     * @return string
     */
    public function getTypeAdhesion()
    {
        return $this->typeAdhesion;
    }

    public function displayHeader(){
        
    }

    public function displayCivility(){
        $txt = '';
        switch ($this->civilite->getLabel()) {
            case 'Monsieur':
                $txt = 'Cher Monsieur,';
                break;
            case 'Madame':
                $txt = 'Chère Madame,';
                break;
            default:
                $txt = 'Chère Madame, cher Monsieur,';
                break;
        }
        return $txt;
    }

    public function populateFromCSV($line){
        try {
            $this->isDossierPaye = false;
            $this->nom = '';
            $this->prenom = '';
            $this->isCA = false;
            $this->isOffreDecouverte = false; 
            $this->numAdh = $line[0];
            if ($line[2] == "True") {
                $this->isActif = false;
            }else{
                $this->isActif = true;
            }
            if ($line[3] != '') {
                $this->dateSortie = new \DateTime($line[3]);
            }
            if ($line[4] != '') {
                $this->dateEntree = new \DateTime($line[4]);
            }
            $this->isRentier = $line[5] == "True" ? true : false;
            $this->nbRentiers = $line[6];
            $this->bp = utf8_encode($line[7]);
            $this->isCourrier = $line[8] == "True" ? true : false;
            $this->isBI = $line[9] == "True" ? true : false;
            $this->isEnvoiIndiv = $line[11] == "True" ? true : false;
            $this->adresse = utf8_encode($line[12]);
            $this->adresseComp = utf8_encode($line[13]);
            $this->cp = $line[14];
            $this->commune = utf8_encode($line[15]);
            $this->pays = utf8_encode($line[16]);
            $this->telFixe = $line[17];
            $this->telPort = $line[18];
            $this->encaisseur = utf8_encode($line[19]);
            $this->mail = utf8_encode($line[20]);
            $this->dateSortie = $line[22] != '' ? new \DateTime($line[22]) : null;
            $this->observation = utf8_encode($line[23]);
            $this->isDossierPaye = $line[24] == "True" ? true : false;

        } catch (ContextErrorException $e) {
            
            return $this;            
        }
        return $this;
    }

    public function populateMembreFromCSV($line){
        $this->nom = '';
        $this->prenom = '';
        $this->fonctionRepresentation = utf8_encode($line[6]);
        $this->dateCIF = $line[7] != '' ? new \DateTime($line[7]) : null;
        $this->isCA = $line[8] == "True" ? true : false;
        $nomPrenom = explode(' ', $line[11]);
        if (sizeof($nomPrenom) > 0) {
            $this->nom = utf8_encode($nomPrenom[0]); 
        }
        if (sizeof($nomPrenom) > 1) {
            $this->prenom = utf8_encode($nomPrenom[1]); 
        }
        if (sizeof($nomPrenom) > 2) {
            $this->prenom = $this->prenom.' '.utf8_encode($nomPrenom[2]); 
        }
        $this->nomJeuneFille = utf8_encode($line[12]);
        $this->dateNaissance = $line[13] ? new \DateTime($line[13]) : null;
        $this->lieuNaissance = utf8_encode($line[14]);
        $this->numSecu = $line[16];
        $this->mentionDeces = $line[19];
        $this->dateDeces = $line[20] ? new \DateTime($line[20]) : null;


        return $this;
    }
}
