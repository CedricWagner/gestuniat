<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OffreDecouverteEnvoi
 *
 * @ORM\Table(name="offre_decouverte_envoi")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OffreDecouverteEnvoiRepository")
 */
class OffreDecouverteEnvoi
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
     * Set envoiRentier
     *
     * @param \AppBundle\Entity\EnvoiRentier $envoiRentier
     *
     * @return OffreDecouverteEnvoi
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
     * @return OffreDecouverteEnvoi
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
