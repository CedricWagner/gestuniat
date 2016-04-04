<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContratPrevObs
 *
 * @ORM\Table(name="contrat_prev_obs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContratPrevObsRepository")
 */
class ContratPrevObs
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
     * @ORM\Column(name="cible", type="string", length=20, nullable=true)
     */
    private $cible;

    /**
     * @var int
     *
     * @ORM\Column(name="numContrat", type="integer", unique=true)
     */
    private $numContrat;

    /**
     * @var string
     *
     * @ORM\Column(name="option", type="string", length=155, nullable=true)
     */
    private $option;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateVersement", type="date", nullable=true)
     */
    private $dateVersement;

    /**
     * @var bool
     *
     * @ORM\Column(name="isResilie", type="boolean")
     */
    private $isResilie;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRes", type="date", nullable=true)
     */
    private $dateRes;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     */
    private $commentaire;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEffet", type="date", nullable=true)
     */
    private $dateEffet;



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
     * Set cible
     *
     * @param string $cible
     *
     * @return ContratPrevObs
     */
    public function setCible($cible)
    {
        $this->cible = $cible;

        return $this;
    }

    /**
     * Get cible
     *
     * @return string
     */
    public function getCible()
    {
        return $this->cible;
    }

    /**
     * Set numContrat
     *
     * @param integer $numContrat
     *
     * @return ContratPrevObs
     */
    public function setNumContrat($numContrat)
    {
        $this->numContrat = $numContrat;

        return $this;
    }

    /**
     * Get numContrat
     *
     * @return int
     */
    public function getNumContrat()
    {
        return $this->numContrat;
    }

    /**
     * Set option
     *
     * @param string $option
     *
     * @return ContratPrevObs
     */
    public function setOption($option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set dateVersement
     *
     * @param \DateTime $dateVersement
     *
     * @return ContratPrevObs
     */
    public function setDateVersement($dateVersement)
    {
        $this->dateVersement = $dateVersement;

        return $this;
    }

    /**
     * Get dateVersement
     *
     * @return \DateTime
     */
    public function getDateVersement()
    {
        return $this->dateVersement;
    }

    /**
     * Set isResilie
     *
     * @param boolean $isResilie
     *
     * @return ContratPrevObs
     */
    public function setIsResilie($isResilie)
    {
        $this->isResilie = $isResilie;

        return $this;
    }

    /**
     * Get isResilie
     *
     * @return bool
     */
    public function getIsResilie()
    {
        return $this->isResilie;
    }

    /**
     * Set dateRes
     *
     * @param \DateTime $dateRes
     *
     * @return ContratPrevObs
     */
    public function setDateRes($dateRes)
    {
        $this->dateRes = $dateRes;

        return $this;
    }

    /**
     * Get dateRes
     *
     * @return \DateTime
     */
    public function getDateRes()
    {
        return $this->dateRes;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return ContratPrevObs
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set dateEffet
     *
     * @param \DateTime $dateEffet
     *
     * @return ContratPrevObs
     */
    public function setDateEffet($dateEffet)
    {
        $this->dateEffet = $dateEffet;

        return $this;
    }

    /**
     * Get dateEffet
     *
     * @return \DateTime
     */
    public function getDateEffet()
    {
        return $this->dateEffet;
    }
}
