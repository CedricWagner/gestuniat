<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrganismeEnvoi
 *
 * @ORM\Table(name="organisme_envoi")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrganismeEnvoiRepository")
 */
class OrganismeEnvoi
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
     * @ORM\ManyToOne(targetEntity="Organisme")
     * @ORM\JoinColumn(name="organisme_id", referencedColumnName="id")
     */
    protected $organisme;

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
     * @return OrganismeEnvoi
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
     * Set organisme
     *
     * @param \AppBundle\Entity\Organisme $organisme
     *
     * @return OrganismeEnvoi
     */
    public function setOrganisme(\AppBundle\Entity\Organisme $organisme = null)
    {
        $this->organisme = $organisme;

        return $this;
    }

    /**
     * Get organisme
     *
     * @return \AppBundle\Entity\Organisme
     */
    public function getOrganisme()
    {
        return $this->organisme;
    }
}
