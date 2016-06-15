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
     * @var \DateTime
     *
     * @ORM\Column(name="dateGenFacture", type="date", nullable=true)
     */
    private $dateGenFacture;

    /**
     * @var string
     *
     * @ORM\Column(name="coutEnvoisIndiv", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $coutEnvoisIndiv;

    /**
     * @ORM\ManyToOne(targetEntity="Section")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    protected $section;

    private $envoisIndiv;
    private $envoisRentiers;
    private $organismes;
    private $offresDecouvertes;


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

    /**
     * Set coutEnvoisIndiv
     *
     * @param string $coutEnvoisIndiv
     *
     * @return EnvoiRentier
     */
    public function setCoutEnvoisIndiv($coutEnvoisIndiv)
    {
        $this->coutEnvoisIndiv = $coutEnvoisIndiv;

        return $this;
    }

    /**
     * Get coutEnvoisIndiv
     *
     * @return string
     */
    public function getCoutEnvoisIndiv()
    {
        return $this->coutEnvoisIndiv;
    }

    public function setEnvoisIndiv($envoisIndiv)
    {
        $this->envoisIndiv = $envoisIndiv;

        return $this;
    }

    public function getEnvoisIndiv()
    {
        return $this->envoisIndiv;
    }

    public function setEnvoisRentiers($envoisRentiers)
    {
        $this->envoisRentiers = $envoisRentiers;

        return $this;
    }

    public function getEnvoisRentiers()
    {
        return $this->envoisRentiers;
    }

    public function setOrganismes($organismes)
    {
        $this->organismes = $organismes;

        return $this;
    }

    public function getOrganismes()
    {
        return $this->organismes;
    }

    public function setOffresDecouvertes($offresDecouvertes)
    {
        $this->offresDecouvertes = $offresDecouvertes;

        return $this;
    }

    public function getOffresDecouvertes()
    {
        return $this->offresDecouvertes;
    }

    /**
     * Set dateGenFacture
     *
     * @param \DateTime $dateGenFacture
     *
     * @return EnvoiRentier
     */
    public function setDateGenFacture($dateGenFacture)
    {
        $this->dateGenFacture = $dateGenFacture;

        return $this;
    }

    /**
     * Get dateGenFacture
     *
     * @return \DateTime
     */
    public function getDateGenFacture()
    {
        return $this->dateGenFacture;
    }
}
