<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContratPrevoyance
 *
 * @ORM\Table(name="contrat_prevoyance")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContratPrevoyanceRepository")
 */
class ContratPrevoyance
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
     * @ORM\Column(name="cible", type="string", length=20, nullable=true)
     */
    private $cible;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=20, nullable=true)
     */
    private $etat;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="numContrat", type="string", length=40)
     */
    private $numContrat;

    /**
     * @var string
     *
     * @ORM\Column(name="option", type="string", length=255, nullable=true)
     */
    private $option;

    /**
     * @var string
     *
     * @ORM\Column(name="nomPrenomAD", type="string", length=255, nullable=true)
     */
    private $nomPrenomAD;

    /**
     * @var string
     *
     * @ORM\Column(name="comGarantie", type="string", length=255, nullable=true)
     */
    private $comGarantie;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEffet", type="date", nullable=true)
     */
    private $dateEffet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModif", type="date", nullable=true)
     */
    private $dateModif;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEffetModif", type="date", nullable=true)
     */
    private $dateEffetModif;

    /**
     * @var string
     *
     * @ORM\Column(name="optionPrec", type="string", length=255, nullable=true)
     */
    private $optionPrec;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRes", type="date", nullable=true)
     */
    private $dateRes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEffetRes", type="date", nullable=true)
     */
    private $dateEffetRes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateConfirmAGGR", type="date", nullable=true)
     */
    private $dateConfirmAGGR;

    /**
     * @var bool
     *
     * @ORM\Column(name="isAutreMutu", type="boolean")
     */
    private $isAutreMutu;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     */
    private $commentaire;
    
    /**
     * @ORM\ManyToOne(targetEntity="RegimeAffiliation")
     * @ORM\JoinColumn(name="regime_aff_id", referencedColumnName="id")
     */
    protected $regimeAffiliation;    

    /**
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    protected $contact;

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
     * Set cible
     *
     * @param string $cible
     *
     * @return ContratPrevoyance
     */
    public function setCible($cible)
    {
        $this->cible = $cible;

        return $this;
    }

    /**
     * Get cible
     *
     * @return string
     */
    public function getCible()
    {
        return $this->cible;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return ContratPrevoyance
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set numContrat
     *
     * @param integer $numContrat
     *
     * @return ContratPrevoyance
     */
    public function setNumContrat($numContrat)
    {
        $this->numContrat = $numContrat;

        return $this;
    }

    /**
     * Get numContrat
     *
     * @return int
     */
    public function getNumContrat()
    {
        return $this->numContrat;
    }

    /**
     * Set option
     *
     * @param string $option
     *
     * @return ContratPrevoyance
     */
    public function setOption($option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set comGarantie
     *
     * @param string $comGarantie
     *
     * @return ContratPrevoyance
     */
    public function setComGarantie($comGarantie)
    {
        $this->comGarantie = $comGarantie;

        return $this;
    }

    /**
     * Get comGarantie
     *
     * @return string
     */
    public function getComGarantie()
    {
        return $this->comGarantie;
    }

    /**
     * Set dateModif
     *
     * @param \DateTime $dateModif
     *
     * @return ContratPrevoyance
     */
    public function setDateModif($dateModif)
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    /**
     * Get dateModif
     *
     * @return \DateTime
     */
    public function getDateModif()
    {
        return $this->dateModif;
    }

    /**
     * Set dateEffetModif
     *
     * @param \DateTime $dateEffetModif
     *
     * @return ContratPrevoyance
     */
    public function setDateEffetModif($dateEffetModif)
    {
        $this->dateEffetModif = $dateEffetModif;

        return $this;
    }

    /**
     * Get dateEffetModif
     *
     * @return \DateTime
     */
    public function getDateEffetModif()
    {
        return $this->dateEffetModif;
    }

    /**
     * Set optionPrec
     *
     * @param string $optionPrec
     *
     * @return ContratPrevoyance
     */
    public function setOptionPrec($optionPrec)
    {
        $this->optionPrec = $optionPrec;

        return $this;
    }

    /**
     * Get optionPrec
     *
     * @return string
     */
    public function getOptionPrec()
    {
        return $this->optionPrec;
    }

    /**
     * Set dateRes
     *
     * @param \DateTime $dateRes
     *
     * @return ContratPrevoyance
     */
    public function setDateRes($dateRes)
    {
        $this->dateRes = $dateRes;

        return $this;
    }

    /**
     * Get dateRes
     *
     * @return \DateTime
     */
    public function getDateRes()
    {
        return $this->dateRes;
    }

    /**
     * Set dateEffetRes
     *
     * @param \DateTime $dateEffetRes
     *
     * @return ContratPrevoyance
     */
    public function setDateEffetRes($dateEffetRes)
    {
        $this->dateEffetRes = $dateEffetRes;

        return $this;
    }

    /**
     * Get dateEffetRes
     *
     * @return \DateTime
     */
    public function getDateEffetRes()
    {
        return $this->dateEffetRes;
    }

    /**
     * Set dateConfirmAGGR
     *
     * @param \DateTime $dateConfirmAGGR
     *
     * @return ContratPrevoyance
     */
    public function setDateConfirmAGGR($dateConfirmAGGR)
    {
        $this->dateConfirmAGGR = $dateConfirmAGGR;

        return $this;
    }

    /**
     * Get dateConfirmAGGR
     *
     * @return \DateTime
     */
    public function getDateConfirmAGGR()
    {
        return $this->dateConfirmAGGR;
    }

    /**
     * Set isAutreMutu
     *
     * @param boolean $isAutreMutu
     *
     * @return ContratPrevoyance
     */
    public function setIsAutreMutu($isAutreMutu)
    {
        $this->isAutreMutu = $isAutreMutu;

        return $this;
    }

    /**
     * Get isAutreMutu
     *
     * @return bool
     */
    public function getIsAutreMutu()
    {
        return $this->isAutreMutu;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return ContratPrevoyance
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set regimeAffiliation
     *
     * @param \RegimeAffiliation $regimeAffiliation
     *
     * @return ContratPrevoyance
     */
    public function setRegimeAffiliation($regimeAffiliation)
    {
        $this->regimeAffiliation = $regimeAffiliation;

        return $this;
    }

    /**
     * Get regimeAffiliation
     *
     * @return \RegimeAffiliation
     */
    public function getRegimeAffiliation()
    {
        return $this->regimeAffiliation;
    }

    /**
     * Set dateEffet
     *
     * @param \DateTime $dateEffet
     *
     * @return ContratPrevoyance
     */
    public function setDateEffet($dateEffet)
    {
        $this->dateEffet = $dateEffet;

        return $this;
    }

    /**
     * Get dateEffet
     *
     * @return \DateTime
     */
    public function getDateEffet()
    {
        return $this->dateEffet;
    }

    /**
     * Set nomPrenomAD
     *
     * @param string $nomPrenomAD
     *
     * @return ContratPrevoyance
     */
    public function setNomPrenomAD($nomPrenomAD)
    {
        $this->nomPrenomAD = $nomPrenomAD;

        return $this;
    }

    /**
     * Get nomPrenomAD
     *
     * @return string
     */
    public function getNomPrenomAD()
    {
        return $this->nomPrenomAD;
    }

    /**
     * Set contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return ContratPrevoyance
     */
    public function setContact(\AppBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \AppBundle\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    public function populateFromCSV($line){
        $this->dateEffet = $line[2] != "" ? new \DateTime($line[2]) : null;
        $this->option = utf8_encode($line[3]);
        $this->optionPrec = utf8_encode($line[4]);
        $this->numContrat = utf8_encode($line[6]);
        $this->commentaire = '';
        if($line[7]!=""){
            $this->commentaire = 'Nature : '.utf8_encode($line[7]).' / ';
        }
        $this->comGarantie = utf8_encode($line[9]);
        $this->dateConfirmAGGR = $line[10] != "" ? new \DateTime($line[10]) : null;
        $this->dateModif = $line[18] != "" ? new \DateTime($line[18]) : null;
        $this->dateEffetModif = $line[20] != "" ? new \DateTime($line[20]) : null;
        $this->dateRes = $line[22] != "" ? new \DateTime($line[22]) : null;
        $this->dateEffetRes = $line[24] != "" ? new \DateTime($line[24]) : null;
        if ($line[26]!="") {
           $this->commentaire = $this->commentaire.' '.utf8_encode($line[26])." / ";
        }
        $this->isAutreMutu = $line[27] == "True" ? true : false;
        if ($line[28]!="") {
            $this->commentaire = $this->commentaire.' '.utf8_encode($line[28]);
        }
        $this->cible = "CONTACT";

        return $this;
    }

    public function populateFromCSV2($line){
        $this->dateEffet = $line[11] != "" ? new \DateTime($line[11]) : null;
        $this->option = utf8_encode($line[12]);
        $this->optionPrec = utf8_encode($line[5]);
        $this->numContrat = utf8_encode($line[13]);
        $this->commentaire = '';
        if($line[14]!=""){
            $this->commentaire = 'Nature : '.utf8_encode($line[14]).' / ';
        }
        $this->comGarantie = utf8_encode($line[16]);
        $this->dateConfirmAGGR = $line[17] != "" ? new \DateTime($line[17]) : null;
        $this->dateModif = $line[19] != "" ? new \DateTime($line[19]) : null;
        $this->dateEffetModif = $line[21] != "" ? new \DateTime($line[21]) : null;
        $this->dateRes = $line[23] != "" ? new \DateTime($line[23]) : null;
        $this->dateEffetRes = $line[25] != "" ? new \DateTime($line[25]) : null;
        if ($line[26]!="") {
           $this->commentaire = $this->commentaire.' '.utf8_encode($line[26])." / ";
        }
        $this->isAutreMutu = $line[27] == "True" ? true : false;
        if ($line[28]!="") {
            $this->commentaire = $this->commentaire.' '.utf8_encode($line[28]);
        }
        $this->cible = "CONTACT";

        return $this;
    }
}
