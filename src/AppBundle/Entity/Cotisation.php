<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cotisation
 *
 * @ORM\Table(name="cotisation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CotisationRepository")
 */
class Cotisation
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
     * @ORM\Column(name="datePaiement", type="date", nullable=true)
     */
    private $datePaiement;

    /**
     * @var bool
     *
     * @ORM\Column(name="isSemestriel", type="boolean")
     */
    private $isSemestriel;

    /**
     * @var string
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="montant", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $montant;

    /**
     * @var \DateTime
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Assert\Type(type="float", message="La valeur saisie n'est pas un montant valide")
     * @ORM\Column(name="dateCreation", type="date")
     */
    private $dateCreation;

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
     * Set datePaiement
     *
     * @param \DateTime $datePaiement
     *
     * @return Cotisation
     */
    public function setDatePaiement($datePaiement)
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    /**
     * Get datePaiement
     *
     * @return \DateTime
     */
    public function getDatePaiement()
    {
        return $this->datePaiement;
    }

    /**
     * Set isSemestriel
     *
     * @param boolean $isSemestriel
     *
     * @return Cotisation
     */
    public function setIsSemestriel($isSemestriel)
    {
        $this->isSemestriel = $isSemestriel;

        return $this;
    }

    /**
     * Get isSemestriel
     *
     * @return bool
     */
    public function getIsSemestriel()
    {
        return $this->isSemestriel;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return Cotisation
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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Cotisation
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return Cotisation
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
     * @return Cotisation
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
}
