<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Suivi
 *
 * @ORM\Table(name="suivi")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SuiviRepository")
 */
class Suivi
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
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEcheance", type="date", nullable=true)
     */
    private $dateEcheance;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $texte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEdition", type="date", nullable=true)
     */
    private $dateEdition;

    /**
     * @var bool
     *
     * @ORM\Column(name="isOk", type="boolean")
     */
    private $isOk;

    /**
     * @ORM\ManyToOne(targetEntity="Contact")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    protected $contact;

    /**
     * @ORM\ManyToOne(targetEntity="Dossier")
     * @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
     */
    protected $dossier;

    /**
     * @ORM\ManyToOne(targetEntity="Operateur")
     * @ORM\JoinColumn(name="operateur_id", referencedColumnName="id")
     */
    protected $operateur;

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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Suivi
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
     * @return Suivi
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
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        if ($this->dossier) {
            return '<a href="/contact/'.$this->dossier->getContact()->getId().'/dossier/'.$this->dossier->getId().'">Dossier "'.$this->dossier->getNom().'"</a>';
        }
        if ($this->section) {
            return '<a href="/section/'.$this->section->getId().'/accueil'.'">Section "'.$this->section->getNom().'"</a>';
        }
        if ($this->contact) {
            return '<a href="/contact/'.$this->contact->getId().'">'.$this->contact->getPrenom().' '.$this->contact->getNom().'</a>';
        }
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Suivi
     */
    public function setTexte($texte)
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getTexte()
    {
        return $this->texte;
    }

    /**
     * Set dateEdition
     *
     * @param \DateTime $dateEdition
     *
     * @return Suivi
     */
    public function setDateEdition($dateEdition)
    {
        $this->dateEdition = $dateEdition;

        return $this;
    }

    /**
     * Get dateEdition
     *
     * @return \DateTime
     */
    public function getDateEdition()
    {
        return $this->dateEdition;
    }

    /**
     * Set isOk
     *
     * @param boolean $isOk
     *
     * @return Suivi
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
     * Set contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return Suivi
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

    /**
     * Set dossier
     *
     * @param \AppBundle\Entity\Dossier $dossier
     *
     * @return Suivi
     */
    public function setDossier(\AppBundle\Entity\Dossier $dossier = null)
    {
        $this->dossier = $dossier;

        return $this;
    }

    /**
     * Get dossier
     *
     * @return \AppBundle\Entity\Dossier
     */
    public function getDossier()
    {
        return $this->dossier;
    }

    /**
     * Set operateur
     *
     * @param \AppBundle\Entity\Operateur $operateur
     *
     * @return Suivi
     */
    public function setOperateur(\AppBundle\Entity\Operateur $operateur = null)
    {
        $this->operateur = $operateur;

        return $this;
    }

    /**
     * Get operateur
     *
     * @return \AppBundle\Entity\Operateur
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
        return 'suivi';
    }

    /**
     * Set section
     *
     * @param \AppBundle\Entity\Section $section
     *
     * @return Suivi
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
