<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EnvoiRentier
 *
 * @ORM\Table(name="envoi_rentier")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EnvoiRentierRepository")
 */
class EnvoiRentier
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
     * @ORM\Column(name="annee", type="integer")
     */
    private $annee;

    /**
     * @var int
     *
     * @ORM\Column(name="numTrimestre", type="integer")
     */
    private $numTrimestre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Section")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    protected $section;


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
     * Set annee
     *
     * @param integer $annee
     *
     * @return EnvoiRentier
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return int
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set numTrimestre
     *
     * @param integer $numTrimestre
     *
     * @return EnvoiRentier
     */
    public function setNumTrimestre($numTrimestre)
    {
        $this->numTrimestre = $numTrimestre;

        return $this;
    }

    /**
     * Get numTrimestre
     *
     * @return int
     */
    public function getNumTrimestre()
    {
        return $this->numTrimestre;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return EnvoiRentier
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
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return EnvoiRentier
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
}

