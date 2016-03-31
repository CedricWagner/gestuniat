<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FiltrePerso
 *
 * @ORM\Table(name="filtre_perso")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FiltrePersoRepository")
 */
class FiltrePerso
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
     * @ORM\Column(name="label", type="string", length=200)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="contexte", type="string", length=20)
     */
    private $contexte;

    /**
     * @ORM\ManyToOne(targetEntity="Operateur", inversedBy="filtresPerso")
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
     * @return FiltrePerso
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
     * Set operateur
     *
     * @param \Operateur $operateur
     *
     * @return FiltrePerso
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
     * Set contexte
     *
     * @param string $contexte
     *
     * @return FiltrePerso
     */
    public function setContexte($contexte)
    {
        $this->contexte = $contexte;

        return $this;
    }

    /**
     * Get contexte
     *
     * @return string
     */
    public function getContexte()
    {
        return $this->contexte;
    }
}

