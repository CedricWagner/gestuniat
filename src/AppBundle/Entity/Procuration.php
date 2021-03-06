<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Procuration
 *
 * @ORM\Table(name="procuration")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProcurationRepository")
 */
class Procuration
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="lieu", type="string", length=255, nullable=true)
     */
    private $lieu;

    /**
     * @var string
     *
     * @ORM\Column(name="mention", type="text", nullable=true)
     */
    private $mention;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=20, nullable=true)
     */
    private $statut;

    /**
     * @var bool
     *
     * @ORM\Column(name="isRecevoirCorres", type="boolean")
     */
    private $isRecevoirCorres;

    /**
     * @var bool
     *
     * @ORM\Column(name="isCourrierRep", type="boolean")
     */
    private $isCourrierRep;

    /**
     * @var bool
     *
     * @ORM\Column(name="isCompletQues", type="boolean")
     */
    private $isCompletQues;

    /**
     * @var bool
     *
     * @ORM\Column(name="isDemandeRens", type="boolean")
     */
    private $isDemandeRens;

    /**
     * @var bool
     *
     * @ORM\Column(name="isRelContest", type="boolean")
     */
    private $isRelContest;

    /**
     * @var bool
     *
     * @ORM\Column(name="isRelPaiement", type="boolean")
     */
    private $isRelPaiement;

    /**
     * @var bool
     *
     * @ORM\Column(name="isRelChangement", type="boolean")
     */
    private $isRelChangement;

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Procuration
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set lieu
     *
     * @param string $lieu
     *
     * @return Procuration
     */
    public function setLieu($lieu)
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Get lieu
     *
     * @return string
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * Set mention
     *
     * @param string $mention
     *
     * @return Procuration
     */
    public function setMention($mention)
    {
        $this->mention = $mention;

        return $this;
    }

    /**
     * Get mention
     *
     * @return string
     */
    public function getMention()
    {
        return $this->mention;
    }

    /**
     * Set statut
     *
     * @param string $statut
     *
     * @return Procuration
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set isRecevoirCorres
     *
     * @param boolean $isRecevoirCorres
     *
     * @return Procuration
     */
    public function setIsRecevoirCorres($isRecevoirCorres)
    {
        $this->isRecevoirCorres = $isRecevoirCorres;

        return $this;
    }

    /**
     * Get isRecevoirCorres
     *
     * @return bool
     */
    public function getIsRecevoirCorres()
    {
        return $this->isRecevoirCorres;
    }

    /**
     * Set isCourrierRep
     *
     * @param boolean $isCourrierRep
     *
     * @return Procuration
     */
    public function setIsCourrierRep($isCourrierRep)
    {
        $this->isCourrierRep = $isCourrierRep;

        return $this;
    }

    /**
     * Get isCourrierRep
     *
     * @return bool
     */
    public function getIsCourrierRep()
    {
        return $this->isCourrierRep;
    }

    /**
     * Set isCompletQues
     *
     * @param boolean $isCompletQues
     *
     * @return Procuration
     */
    public function setIsCompletQues($isCompletQues)
    {
        $this->isCompletQues = $isCompletQues;

        return $this;
    }

    /**
     * Get isCompletQues
     *
     * @return bool
     */
    public function getIsCompletQues()
    {
        return $this->isCompletQues;
    }

    /**
     * Set isRelContest
     *
     * @param boolean $isRelContest
     *
     * @return Procuration
     */
    public function setIsRelContest($isRelContest)
    {
        $this->isRelContest = $isRelContest;

        return $this;
    }

    /**
     * Get isRelContest
     *
     * @return bool
     */
    public function getIsRelContest()
    {
        return $this->isRelContest;
    }

    /**
     * Set isRelPaiement
     *
     * @param boolean $isRelPaiement
     *
     * @return Procuration
     */
    public function setIsRelPaiement($isRelPaiement)
    {
        $this->isRelPaiement = $isRelPaiement;

        return $this;
    }

    /**
     * Get isRelPaiement
     *
     * @return bool
     */
    public function getIsRelPaiement()
    {
        return $this->isRelPaiement;
    }

    /**
     * Set isRelChangement
     *
     * @param boolean $isRelChangement
     *
     * @return Procuration
     */
    public function setIsRelChangement($isRelChangement)
    {
        $this->isRelChangement = $isRelChangement;

        return $this;
    }

    /**
     * Get isRelChangement
     *
     * @return bool
     */
    public function getIsRelChangement()
    {
        return $this->isRelChangement;
    }

    /**
     * Set contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return Procuration
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

    /**
     * Set isDemandeRens
     *
     * @param boolean $isDemandeRens
     *
     * @return Procuration
     */
    public function setIsDemandeRens($isDemandeRens)
    {
        $this->isDemandeRens = $isDemandeRens;

        return $this;
    }

    /**
     * Get isDemandeRens
     *
     * @return boolean
     */
    public function getIsDemandeRens()
    {
        return $this->isDemandeRens;
    }

    public function populateFromCSV($line){
        $this->lieu = utf8_encode($line[3]);
        $this->date = $line[4] != "" ? new \DateTime($line[4]) : null;
        $this->isRecevoirCorres = $line[5] == "True" ? true : false;
        $this->isCourrierRep = $line[6] == "True" ? true : false;
        $this->isCompletQues = $line[7] == "True" ? true : false;
        $this->isDemandeRens = $line[8] == "True" ? true : false;
        $this->isRelContest = $line[13] == "True" ? true : false;
        $this->isRelPaiement = $line[14] == "True" ? true : false;
        $this->isRelChangement = $line[15] == "True" ? true : false;

        return $this;
    }

}
