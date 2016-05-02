<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RemiseTimbre
 *
 * @ORM\Table(name="remise_timbre")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RemiseTimbreRepository")
 */
class RemiseTimbre
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
     * @ORM\Column(name="dateRemise", type="date")
     */
    private $dateRemise;

    /**
     * @var int
     *
     * @ORM\Column(name="nbEmis", type="integer")
     */
    private $nbEmis;

    /**
     * @var int
     *
     * @ORM\Column(name="nbRemis", type="integer")
     */
    private $nbRemis;

    /**
     * @var int
     *
     * @ORM\Column(name="nbPayes", type="integer")
     */
    private $nbPayes;

    /**
     * @var bool
     *
     * @ORM\Column(name="isAnnuel", type="boolean")
     */
    private $isAnnuel;

    /**
     * @var int
     *
     * @ORM\Column(name="annee", type="integer")
     */
    private $annee;

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
     * Set dateRemise
     *
     * @param \DateTime $dateRemise
     *
     * @return RemiseTimbre
     */
    public function setDateRemise($dateRemise)
    {
        $this->dateRemise = $dateRemise;

        return $this;
    }

    /**
     * Get dateRemise
     *
     * @return \DateTime
     */
    public function getDateRemise()
    {
        return $this->dateRemise;
    }

    /**
     * Set nbEmis
     *
     * @param integer $nbEmis
     *
     * @return RemiseTimbre
     */
    public function setNbEmis($nbEmis)
    {
        $this->nbEmis = $nbEmis;

        return $this;
    }

    /**
     * Get nbEmis
     *
     * @return int
     */
    public function getNbEmis()
    {
        return $this->nbEmis;
    }

    /**
     * Set nbRemis
     *
     * @param integer $nbRemis
     *
     * @return RemiseTimbre
     */
    public function setNbRemis($nbRemis)
    {
        $this->nbRemis = $nbRemis;

        return $this;
    }

    /**
     * Get nbRemis
     *
     * @return int
     */
    public function getNbRemis()
    {
        return $this->nbRemis;
    }

    /**
     * Set nbPayes
     *
     * @param integer $nbPayes
     *
     * @return RemiseTimbre
     */
    public function setNbPayes($nbPayes)
    {
        $this->nbPayes = $nbPayes;

        return $this;
    }

    /**
     * Get nbPayes
     *
     * @return int
     */
    public function getNbPayes()
    {
        return $this->nbPayes;
    }

    /**
     * Set isAnnuel
     *
     * @param boolean $isAnnuel
     *
     * @return RemiseTimbre
     */
    public function setIsAnnuel($isAnnuel)
    {
        $this->isAnnuel = $isAnnuel;

        return $this;
    }

    /**
     * Get isAnnuel
     *
     * @return bool
     */
    public function getIsAnnuel()
    {
        return $this->isAnnuel;
    }

    /**
     * Set annee
     *
     * @param integer $annee
     *
     * @return RemiseTimbre
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
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return RemiseTimbre
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