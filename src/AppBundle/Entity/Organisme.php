<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Organisme
 *
 * @ORM\Table(name="organisme")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrganismeRepository")
 */
class Organisme
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="nomTitulaire", type="string", length=255)
     */
    private $nomTitulaire;

    /**
     * @var string
     *
     * @ORM\Column(name="fonctionTitulaire", type="string", length=255)
     */
    private $fonctionTitulaire;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="adresseComp", type="text", nullable=true)
     */
    private $adresseComp;

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=8, nullable=true)
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=100, nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=20, nullable=true)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="bp", type="string", length=50, nullable=true)
     */
    private $bp;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=20, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\ManyToOne(targetEntity="TypeOrganisme")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    protected $typeOrganisme;

    /**
     * @ORM\ManyToOne(targetEntity="Civilite")
     * @ORM\JoinColumn(name="civilite_id", referencedColumnName="id")
     */
    protected $civilite;


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
     * Set nom
     *
     * @param string $nom
     *
     * @return Organisme
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nomTitulaire
     *
     * @param string $nomTitulaire
     *
     * @return Organisme
     */
    public function setNomTitulaire($nomTitulaire)
    {
        $this->nomTitulaire = $nomTitulaire;

        return $this;
    }

    /**
     * Get nomTitulaire
     *
     * @return string
     */
    public function getNomTitulaire()
    {
        return $this->nomTitulaire;
    }

    /**
     * Set fonctionTitulaire
     *
     * @param string $fonctionTitulaire
     *
     * @return Organisme
     */
    public function setFonctionTitulaire($fonctionTitulaire)
    {
        $this->fonctionTitulaire = $fonctionTitulaire;

        return $this;
    }

    /**
     * Get fonctionTitulaire
     *
     * @return string
     */
    public function getFonctionTitulaire()
    {
        return $this->fonctionTitulaire;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Organisme
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set adresseComp
     *
     * @param string $adresseComp
     *
     * @return Organisme
     */
    public function setAdresseComp($adresseComp)
    {
        $this->adresseComp = $adresseComp;

        return $this;
    }

    /**
     * Get adresseComp
     *
     * @return string
     */
    public function getAdresseComp()
    {
        return $this->adresseComp;
    }

    /**
     * Set cp
     *
     * @param string $cp
     *
     * @return Organisme
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Organisme
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Organisme
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set bp
     *
     * @param string $bp
     *
     * @return Organisme
     */
    public function setBp($bp)
    {
        $this->bp = $bp;

        return $this;
    }

    /**
     * Get bp
     *
     * @return string
     */
    public function getBp()
    {
        return $this->bp;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Organisme
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set typeOrganisme
     *
     * @param \AppBundle\Entity\TypeOrganisme $typeOrganisme
     *
     * @return Organisme
     */
    public function setTypeOrganisme(\AppBundle\Entity\TypeOrganisme $typeOrganisme = null)
    {
        $this->typeOrganisme = $typeOrganisme;

        return $this;
    }

    /**
     * Get typeOrganisme
     *
     * @return \AppBundle\Entity\TypeOrganisme
     */
    public function getTypeOrganisme()
    {
        return $this->typeOrganisme;
    }

    /**
     * Set statutMatrimonial
     *
     * @param \AppBundle\Entity\StatutMatrimonial $statutMatrimonial
     *
     * @return Organisme
     */
    public function setStatutMatrimonial(\AppBundle\Entity\StatutMatrimonial $statutMatrimonial = null)
    {
        $this->statutMatrimonial = $statutMatrimonial;

        return $this;
    }

    /**
     * Get statutMatrimonial
     *
     * @return \AppBundle\Entity\StatutMatrimonial
     */
    public function getStatutMatrimonial()
    {
        return $this->statutMatrimonial;
    }

    /**
     * Set civilite
     *
     * @param \AppBundle\Entity\Civilite $civilite
     *
     * @return Organisme
     */
    public function setCivilite(\AppBundle\Entity\Civilite $civilite = null)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get civilite
     *
     * @return \AppBundle\Entity\Civilite
     */
    public function getCivilite()
    {
        return $this->civilite;
    }
}
