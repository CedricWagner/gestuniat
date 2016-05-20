<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Patrimoine
 *
 * @ORM\Table(name="patrimoine")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PatrimoineRepository")
 */
class Patrimoine
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
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="annee", type="integer")
     */
    private $annee;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="valeur", type="decimal", precision=10, scale=2)
     */
    private $valeur;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="interets", type="decimal", precision=10, scale=2)
     */
    private $interets;

    /**
     * @ORM\ManyToOne(targetEntity="Section")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id")
     */
    protected $section;

    protected $fannee;


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
     * @return Patrimoine
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
     * Set valeur
     *
     * @param string $valeur
     *
     * @return Patrimoine
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get valeur
     *
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set interets
     *
     * @param string $interets
     *
     * @return Patrimoine
     */
    public function setInterets($interets)
    {
        $this->interets = $interets;

        return $this;
    }

    /**
     * Get interets
     *
     * @return string
     */
    public function getInterets()
    {
        return $this->interets;
    }

    /**
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return Patrimoine
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

    public function setFannee($fannee)
    {
        $this->fannee = $fannee;

        return $this;
    }

    public function getFannee()
    {
        return $this->annee;
    }
}
