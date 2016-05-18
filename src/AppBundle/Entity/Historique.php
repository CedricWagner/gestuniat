<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Historique
 *
 * @ORM\Table(name="historique")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HistoriqueRepository")
 */
class Historique
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="idEntite", type="integer")
     */
    private $idEntite;

    /**
     * @var int
     *
     * @ORM\Column(name="nameEntite", type="string", length=150)
     */
    private $nomEntite;
    
    /**
     * @var int
     *
     * @ORM\Column(name="action", type="string", length=255)
     */
    private $action;

    /**
     * @ORM\ManyToOne(targetEntity="Operateur")
     * @ORM\JoinColumn(name="operateur_id", referencedColumnName="id")
     */
    protected $operateur;




    /**
     * Get id
     *
     * @return integer
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
     * @return Historique
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
     * Set idEntite
     *
     * @param integer $idEntite
     *
     * @return Historique
     */
    public function setIdEntite($idEntite)
    {
        $this->idEntite = $idEntite;

        return $this;
    }

    /**
     * Get idEntite
     *
     * @return integer
     */
    public function getIdEntite()
    {
        return $this->idEntite;
    }

    /**
     * Set nameEntite
     *
     * @param string $nameEntite
     *
     * @return Historique
     */
    public function setNameEntite($nameEntite)
    {
        $this->nameEntite = $nameEntite;

        return $this;
    }

    /**
     * Get nameEntite
     *
     * @return string
     */
    public function getNameEntite()
    {
        return $this->nameEntite;
    }

    /**
     * Set desc
     *
     * @param string $desc
     *
     * @return Historique
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;

        return $this;
    }

    /**
     * Get desc
     *
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * Set operateur
     *
     * @param \AppBundle\Entity\Operateur $operateur
     *
     * @return Historique
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
     * Set action
     *
     * @param string $action
     *
     * @return Historique
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set nomEntite
     *
     * @param string $nomEntite
     *
     * @return Historique
     */
    public function setNomEntite($nomEntite)
    {
        $this->nomEntite = $nomEntite;

        return $this;
    }

    /**
     * Get nomEntite
     *
     * @return string
     */
    public function getNomEntite()
    {
        return $this->nomEntite;
    }
}
