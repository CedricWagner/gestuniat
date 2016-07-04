<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Permanence
 *
 * @ORM\Table(name="permanence")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PermanenceRepository")
 */
class Permanence
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
     * @ORM\Column(name="dateMAJ", type="date")
     */
    private $dateMAJ;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="horaire", type="string", length=100, nullable=true)
     */
    private $horaire;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="lieu", type="string", length=255)
     */
    private $lieu;

    /**
     * @var string
     *
     * @ORM\Column(name="militants", type="text", nullable=true)
     */
    private $militants;

    /**
     * @ORM\ManyToOne(targetEntity="Section")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    protected $section;

    /**
     * @ORM\ManyToOne(targetEntity="Periodicite")
     * @ORM\JoinColumn(name="periodicite_id", referencedColumnName="id")
     */
    protected $periodicite;

    /**
     * @ORM\ManyToOne(targetEntity="TypeTournee")
     * @ORM\JoinColumn(name="typeTournee_id", referencedColumnName="id")
     */
    protected $typeTournee;


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
     * Set label
     *
     * @param string $label
     *
     * @return Permanence
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set horaire
     *
     * @param string $horaire
     *
     * @return Permanence
     */
    public function setHoraire($horaire)
    {
        $this->horaire = $horaire;

        return $this;
    }

    /**
     * Get horaire
     *
     * @return string
     */
    public function getHoraire()
    {
        return $this->horaire;
    }

    /**
     * Set lieu
     *
     * @param string $lieu
     *
     * @return Permanence
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
     * Set militants
     *
     * @param string $militants
     *
     * @return Permanence
     */
    public function setMilitants($militants)
    {
        $this->militants = $militants;

        return $this;
    }

    /**
     * Get militants
     *
     * @return string
     */
    public function getMilitants()
    {
        return $this->militants;
    }

    /**
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return Permanence
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

    /**
     * Set periodicite
     *
     * @param \AppBundle\Entity\Periodicite $periodicite
     *
     * @return Permanence
     */
    public function setPeriodicite(\AppBundle\Entity\Periodicite $periodicite = null)
    {
        $this->periodicite = $periodicite;

        return $this;
    }

    /**
     * Get periodicite
     *
     * @return \AppBundle\Entity\Periodicite
     */
    public function getPeriodicite()
    {
        return $this->periodicite;
    }

    /**
     * Set typeTournee
     *
     * @param \AppBundle\Entity\TypeTournee $typeTournee
     *
     * @return Permanence
     */
    public function setTypeTournee(\AppBundle\Entity\TypeTournee $typeTournee = null)
    {
        $this->typeTournee = $typeTournee;

        return $this;
    }

    /**
     * Get typeTournee
     *
     * @return \AppBundle\Entity\TypeTournee
     */
    public function getTypeTournee()
    {
        return $this->typeTournee;
    }

    /**
     * Set dateMAJ
     *
     * @param \DateTime $dateMAJ
     *
     * @return Permanence
     */
    public function setDateMAJ($dateMAJ)
    {
        $this->dateMAJ = $dateMAJ;

        return $this;
    }

    /**
     * Get dateMAJ
     *
     * @return \DateTime
     */
    public function getDateMAJ()
    {
        return $this->dateMAJ;
    }


    public function populateFromCSV($line){
        $this->label = utf8_encode($line[3]);
        $this->horaire = utf8_encode($line[4]);
        $this->lieu = utf8_encode($line[5]);
        $this->militants = utf8_encode($line[7].' '.$line[8].' '.$line[9]);
        $this->dateMAJ = new \DateTime($line[11]);
        
        return $this;
    }
}
