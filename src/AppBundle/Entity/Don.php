<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Don
 *
 * @ORM\Table(name="don")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DonRepository")
 */
class Don
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
     * @ORM\Column(name="intermediaire", type="string", length=100, nullable=true)
     */
    private $intermediaire;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="montant", type="decimal", precision=10, scale=2)
     */
    private $montant;

    /**
     * @var bool
     *
     * @ORM\Column(name="isAnonyme", type="boolean")
     */
    private $isAnonyme;
    
    /**
     * @ORM\ManyToOne(targetEntity="Operateur")
     * @ORM\JoinColumn(name="operateur_id", referencedColumnName="id")
     */
    protected $operateur;

    /**
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    protected $contact;

    /**
     * @ORM\ManyToOne(targetEntity="MoyenPaiement")
     * @ORM\JoinColumn(name="moyenPaiement_id", referencedColumnName="id")
     */
    protected $moyenPaiement;


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
     * @return Don
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
     * Set intermediaire
     *
     * @param string $intermediaire
     *
     * @return Don
     */
    public function setIntermediaire($intermediaire)
    {
        $this->intermediaire = $intermediaire;

        return $this;
    }

    /**
     * Get intermediaire
     *
     * @return string
     */
    public function getIntermediaire()
    {
        return $this->intermediaire;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return Don
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set isAnonyme
     *
     * @param boolean $isAnonyme
     *
     * @return Don
     */
    public function setIsAnonyme($isAnonyme)
    {
        $this->isAnonyme = $isAnonyme;

        return $this;
    }

    /**
     * Get isAnonyme
     *
     * @return bool
     */
    public function getIsAnonyme()
    {
        return $this->isAnonyme;
    }

    /**
     * Set operateur
     *
     * @param \AppBundle\Entity\Operateur $operateur
     *
     * @return Don
     */
    public function setOperateur(\AppBundle\Entity\Operateur $operateur = null)
    {
        $this->operateur = $operateur;

        return $this;
    }

    /**
     * Get operateur
     *
     * @return \AppBundle\Entity\Operateur
     */
    public function getOperateur()
    {
        return $this->operateur;
    }

    /**
     * Set contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return Don
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
     * Set moyenPaiement
     *
     * @param \AppBundle\Entity\MoyenPaiement $moyenPaiement
     *
     * @return Don
     */
    public function setMoyenPaiement(\AppBundle\Entity\MoyenPaiement $moyenPaiement = null)
    {
        $this->moyenPaiement = $moyenPaiement;

        return $this;
    }

    /**
     * Get moyenPaiement
     *
     * @return \AppBundle\Entity\MoyenPaiement
     */
    public function getMoyenPaiement()
    {
        return $this->moyenPaiement;
    }

    public function populateFromCSV($line){
        $this->date = new \DateTime($line[3]);
        $this->montant = intval($line[4]);
        $this->intermediaire = utf8_encode($line[5]);
        $this->isAnonyme = $line[6] == "True" ? true : false;

        return $this;
    }
}
