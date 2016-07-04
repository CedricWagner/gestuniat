<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @ORM\Column(name="numContrat", type="string")
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
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    protected $contact;


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

    /**
     * Set contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return ContratPrevObs
     */
    public function setContact(\AppBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return \AppBundle\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    public function populateFromCSV($line){
        $this->dateEffet = $line[5] != "" ? new \DateTime($line[5]) : null;
        $this->numContrat = utf8_encode($line[6]);
        $this->commentaire = utf8_encode($line[7]);
        $this->isResilie = $line[8] == "True" ? true : false;
        $this->dateRes = $line[9] != "" ? new \DateTime($line[9]) : null;
        $this->dateVersement = $line[10] != "" ? new \DateTime($line[10]) : null;
        $this->cible = 'CONTACT';

        return $this;
    }
}
