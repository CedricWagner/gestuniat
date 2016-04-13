<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pouvoir
 *
 * @ORM\Table(name="pouvoir")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PouvoirRepository")
 */
class Pouvoir
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
     * @ORM\Column(name="affaire", type="string", length=50, nullable=true)
     */
    private $affaire;

    /**
     * @var string
     *
     * @ORM\Column(name="destinataire", type="string", length=100, nullable=true)
     */
    private $destinataire;

    /**
     * @var string
     *
     * @ORM\Column(name="lieu", type="string", length=255, nullable=true)
     */
    private $lieu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="isFIVA", type="boolean")
     */
    private $isFIVA;

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
     * Set affaire
     *
     * @param string $affaire
     *
     * @return Pouvoir
     */
    public function setAffaire($affaire)
    {
        $this->affaire = $affaire;

        return $this;
    }

    /**
     * Get affaire
     *
     * @return string
     */
    public function getAffaire()
    {
        return $this->affaire;
    }

    /**
     * Set destinataire
     *
     * @param string $destinataire
     *
     * @return Pouvoir
     */
    public function setDestinataire($destinataire)
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    /**
     * Get destinataire
     *
     * @return string
     */
    public function getDestinataire()
    {
        return $this->destinataire;
    }

    /**
     * Set lieu
     *
     * @param string $lieu
     *
     * @return Pouvoir
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Pouvoir
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
     * Set isFIVA
     *
     * @param boolean $isFIVA
     *
     * @return Pouvoir
     */
    public function setIsFIVA($isFIVA)
    {
        $this->isFIVA = $isFIVA;

        return $this;
    }

    /**
     * Get isFIVA
     *
     * @return bool
     */
    public function getIsFIVA()
    {
        return $this->isFIVA;
    }

    /**
     * Set contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return Pouvoir
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
}
