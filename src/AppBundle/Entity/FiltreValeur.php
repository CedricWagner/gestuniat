<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FiltreValeur
 *
 * @ORM\Table(name="filtre_valeur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FiltreValeurRepository")
 */
class FiltreValeur
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
     * @ORM\Column(name="valeur", type="string", length=100, nullable=true)
     */
    private $valeur;

    /**
     * @ORM\ManyToOne(targetEntity="FiltrePerso")
     * @ORM\JoinColumn(name="filtre_perso_id", referencedColumnName="id")
     */
    protected $filtrePerso;

    /**
     * @ORM\ManyToOne(targetEntity="Champ")
     * @ORM\JoinColumn(name="champ_id", referencedColumnName="id")
     */
    protected $champ;

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
     * Set valeur
     *
     * @param string $valeur
     *
     * @return FiltreValeur
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
     * Set filtrePerso
     *
     * @param \FiltrePerso $filtrePerso
     *
     * @return FiltrePerso
     */
    public function setFiltrePerso($filtrePerso)
    {
        $this->filtrePerso = $filtrePerso;

        return $this;
    }

    /**
     * Get filtrePerso
     *
     * @return \FiltrePerso
     */
    public function getFiltrePerso()
    {
        return $this->filtrePerso;
    }


    /**
     * Set champ
     *
     * @param \Champ $champ
     *
     * @return Champ
     */
    public function setChamp($champ)
    {
        $this->champ = $champ;

        return $this;
    }

    /**
     * Get champ
     *
     * @return \Champ
     */
    public function getChamp()
    {
        return $this->champ;
    }
}

