<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DestRentierEnvoi
 *
 * @ORM\Table(name="dest_rentier_envoi")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DestRentierEnvoiRepository")
 */
class DestRentierEnvoi
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
     * @var int
     *
     * @ORM\Column(name="nb", type="integer")
     */
    private $nb;

    /**
     * @ORM\ManyToOne(targetEntity="EnvoiRentier")
     * @ORM\JoinColumn(name="envoiRentier_id", referencedColumnName="id")
     */
    protected $envoiRentier;

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
     * Set nb
     *
     * @param integer $nb
     *
     * @return DestRentierEnvoi
     */
    public function setNb($nb)
    {
        $this->nb = $nb;

        return $this;
    }

    /**
     * Get nb
     *
     * @return int
     */
    public function getNb()
    {
        return $this->nb;
    }

    /**
     * Set envoiRentier
     *
     * @param \AppBundle\Entity\EnvoiRentier $envoiRentier
     *
     * @return DestRentierEnvoi
     */
    public function setEnvoiRentier(\AppBundle\Entity\EnvoiRentier $envoiRentier = null)
    {
        $this->envoiRentier = $envoiRentier;

        return $this;
    }

    /**
     * Get envoiRentier
     *
     * @return \AppBundle\Entity\EnvoiRentier
     */
    public function getEnvoiRentier()
    {
        return $this->envoiRentier;
    }

    /**
     * Set contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return DestRentierEnvoi
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
