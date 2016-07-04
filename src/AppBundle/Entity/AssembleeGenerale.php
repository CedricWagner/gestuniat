<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssembleeGenerale
 *
 * @ORM\Table(name="assemblee_generale")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssembleeGeneraleRepository")
 */
class AssembleeGenerale
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
     * @ORM\Column(name="heure", type="string", length=50, nullable=true)
     */
    private $heure;

    /**
     * @var string
     *
     * @ORM\Column(name="orateur", type="string", length=100, nullable=true)
     */
    private $orateur;

    /**
     * @var string
     *
     * @ORM\Column(name="lieu", type="string", length=255, nullable=true)
     */
    private $lieu;

    /**
     * @var int
     *
     * @ORM\Column(name="nbParticipants", type="integer", nullable=true)
     */
    private $nbParticipants;

    /**
     * @ORM\ManyToOne(targetEntity="Section")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    private $section;


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
     * @return AssembleeGenerale
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
     * Set heure
     *
     * @param string $heure
     *
     * @return AssembleeGenerale
     */
    public function setHeure($heure)
    {
        $this->heure = $heure;

        return $this;
    }

    /**
     * Get heure
     *
     * @return string
     */
    public function getHeure()
    {
        return $this->heure;
    }

    /**
     * Set orateur
     *
     * @param string $orateur
     *
     * @return AssembleeGenerale
     */
    public function setOrateur($orateur)
    {
        $this->orateur = $orateur;

        return $this;
    }

    /**
     * Get orateur
     *
     * @return string
     */
    public function getOrateur()
    {
        return $this->orateur;
    }

    /**
     * Set lieu
     *
     * @param string $lieu
     *
     * @return AssembleeGenerale
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
     * Set nbParticipants
     *
     * @param integer $nbParticipants
     *
     * @return AssembleeGenerale
     */
    public function setNbParticipants($nbParticipants)
    {
        $this->nbParticipants = $nbParticipants;

        return $this;
    }

    /**
     * Get nbParticipants
     *
     * @return int
     */
    public function getNbParticipants()
    {
        return $this->nbParticipants;
    }

    /**
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return AssembleeGenerale
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

    public function populateFromCSV($line){
        $this->date = new \DateTime($line[3]);
        $this->orateur = utf8_encode($line[4]);
        $this->lieu = utf8_encode($line[6]);
        $this->heure = utf8_encode($line[13]);
        $this->nbParticipants = utf8_encode($line[12]);

        return $this;
    }
}
