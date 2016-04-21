<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Section
 *
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectionRepository")
 */
class Section
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
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="subventions", type="string", length=255, nullable=true)
     */
    private $subventions;

    /**
     * @var string
     *
     * @ORM\Column(name="destDernierListing", type="string", length=255, nullable=true)
     */
    private $destDernierListing;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDernierListing", type="date", nullable=true)
     */
    private $dateDernierListing;

    /**
     * @var string
     *
     * @ORM\Column(name="numBulletin", type="string", length=10, nullable=true)
     */
    private $numBulletin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRemiseBulletin", type="date", nullable=true)
     */
    private $dateRemiseBulletin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateMaj", type="date", nullable=true)
     */
    private $dateMaj;

    /**
     * @var string
     *
     * @ORM\Column(name="infosComp", type="text", nullable=true)
     */
    private $infosComp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDerniereAG", type="date", nullable=true)
     */
    private $dateDerniereAG;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=20, nullable=true)
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumn(name="delegue_id", referencedColumnName="id")
     */
    protected $delegue;


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
     * @return Section
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
     * Set subventions
     *
     * @param string $subventions
     *
     * @return Section
     */
    public function setSubventions($subventions)
    {
        $this->subventions = $subventions;

        return $this;
    }

    /**
     * Get subventions
     *
     * @return string
     */
    public function getSubventions()
    {
        return $this->subventions;
    }

    /**
     * Set destDernierListing
     *
     * @param string $destDernierListing
     *
     * @return Section
     */
    public function setDestDernierListing($destDernierListing)
    {
        $this->destDernierListing = $destDernierListing;

        return $this;
    }

    /**
     * Get destDernierListing
     *
     * @return string
     */
    public function getDestDernierListing()
    {
        return $this->destDernierListing;
    }

    /**
     * Set dateDernierListing
     *
     * @param \DateTime $dateDernierListing
     *
     * @return Section
     */
    public function setDateDernierListing($dateDernierListing)
    {
        $this->dateDernierListing = $dateDernierListing;

        return $this;
    }

    /**
     * Get dateDernierListing
     *
     * @return \DateTime
     */
    public function getDateDernierListing()
    {
        return $this->dateDernierListing;
    }

    /**
     * Set numBulletin
     *
     * @param string $numBulletin
     *
     * @return Section
     */
    public function setNumBulletin($numBulletin)
    {
        $this->numBulletin = $numBulletin;

        return $this;
    }

    /**
     * Get numBulletin
     *
     * @return string
     */
    public function getNumBulletin()
    {
        return $this->numBulletin;
    }

    /**
     * Set dateRemiseBulletin
     *
     * @param \DateTime $dateRemiseBulletin
     *
     * @return Section
     */
    public function setDateRemiseBulletin($dateRemiseBulletin)
    {
        $this->dateRemiseBulletin = $dateRemiseBulletin;

        return $this;
    }

    /**
     * Get dateRemiseBulletin
     *
     * @return \DateTime
     */
    public function getDateRemiseBulletin()
    {
        return $this->dateRemiseBulletin;
    }

    /**
     * Set dateMaj
     *
     * @param \DateTime $dateMaj
     *
     * @return Section
     */
    public function setDateMaj($dateMaj)
    {
        $this->dateMaj = $dateMaj;

        return $this;
    }

    /**
     * Get dateMaj
     *
     * @return \DateTime
     */
    public function getDateMaj()
    {
        return $this->dateMaj;
    }

    /**
     * Set infosComp
     *
     * @param string $infosComp
     *
     * @return Section
     */
    public function setInfosComp($infosComp)
    {
        $this->infosComp = $infosComp;

        return $this;
    }

    /**
     * Get infosComp
     *
     * @return string
     */
    public function getInfosComp()
    {
        return $this->infosComp;
    }

    /**
     * Set dateDerniereAG
     *
     * @param \DateTime $dateDerniereAG
     *
     * @return Section
     */
    public function setDateDerniereAG($dateDerniereAG)
    {
        $this->dateDerniereAG = $dateDerniereAG;

        return $this;
    }

    /**
     * Get dateDerniereAG
     *
     * @return \DateTime
     */
    public function getDateDerniereAG()
    {
        return $this->dateDerniereAG;
    }

    /**
     * Set statut
     *
     * @param string $statut
     *
     * @return Section
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
     * Set delegue
     *
     * @param \AppBundle\Entity\Contact $delegue
     *
     * @return Section
     */
    public function setDelegue(\AppBundle\Entity\Contact $delegue = null)
    {
        $this->delegue = $delegue;

        return $this;
    }

    /**
     * Get delegue
     *
     * @return \AppBundle\Entity\Contact
     */
    public function getDelegue()
    {
        return $this->delegue;
    }
}