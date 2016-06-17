<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Alerte
 *
 * @ORM\Table(name="alerte")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AlerteRepository")
 */
class Alerte
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
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="texte", type="text")
     */
    private $texte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime", nullable=true)
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEcheance", type="datetime", nullable=true)
     */
    private $dateEcheance;
    /**
     * @var bool
     *
     * @ORM\Column(name="is_ok", type="boolean", nullable=true)
     */
    private $isOk;
    /**
     * @ORM\ManyToOne(targetEntity="Operateur", inversedBy="alertes")
     * @ORM\JoinColumn(name="operateur_id", referencedColumnName="id")
     */
    protected $operateur;

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
     * @return Alerte
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
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->label;
    }

    /**
     * Set texte
     *
     * @param string $texte
     *
     * @return Alerte
     */
    public function setTexte($texte)
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * Get texte
     *
     * @return string
     */
    public function getTexte()
    {
        return $this->texte;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Alerte
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateEcheance
     *
     * @param \DateTime $dateEcheance
     *
     * @return Alerte
     */
    public function setDateEcheance($dateEcheance)
    {
        $this->dateEcheance = $dateEcheance;

        return $this;
    }

    /**
     * Get dateEcheance
     *
     * @return \DateTime
     */
    public function getDateEcheance()
    {
        return $this->dateEcheance;
    }

    /**
     * Set isOk
     *
     * @param bool $isOk
     *
     * @return Alerte
     */
    public function setIsOk($isOk)
    {
        $this->isOk = $isOk;

        return $this;
    }

    /**
     * Get isOk
     *
     * @return bool
     */
    public function getIsOk()
    {
        return $this->isOk;
    }

    /**
     * Set operateur
     *
     * @param \Operateur $operateur
     *
     * @return Alerte
     */
    public function setOperateur($operateur)
    {
        $this->operateur = $operateur;

        return $this;
    }

    /**
     * Get operateur
     *
     * @return \Operateur
     */
    public function getOperateur()
    {
        return $this->operateur;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return 'alerte';
    }
}

