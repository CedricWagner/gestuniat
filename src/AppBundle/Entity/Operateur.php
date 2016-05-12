<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Operateur
 *
 * @ORM\Table(name="operateur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OperateurRepository")
 */
class Operateur implements UserInterface
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
     * @ORM\Column(name="nom", type="string", length=20, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=20, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=20)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=20)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="mdp", type="string", length=100)
     */
    private $mdp;

    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity="Alerte", mappedBy="operateur")
     */
    protected $alertes;
    /**
     * @ORM\OneToMany(targetEntity="FiltrePerso", mappedBy="operateur")
     */
    protected $filtresPerso;




    public function __construct()
    {
        $this->alertes = new ArrayCollection();
    }

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
     * @return Operateur
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Operateur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return Operateur
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get role label
     *
     * @return string
     */
    public function getRoleLabel()
    {
        switch ($this->role) {
            case 'ADMIN':
                return 'Administrateur';
                break;
            case 'JURIST':
                return 'Juriste';
                break;
            case 'USER':
                return 'Utilisateur';
                break;
            case 'DELETED':
                return 'Supprimé';
                break;
            default:
                return '/!\ Aucun';
                break;
        }
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return Operateur
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set mdp
     *
     * @param string $mdp
     *
     * @return Operateur
     */
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;

        return $this;
    }

    /**
     * Get mdp
     *
     * @return string
     */
    public function getMdp()
    {
        return $this->mdp;
    }


    public function getRoles()
    {

        $roles = ['ROLE_USER'];

        if($this->getRole()=="ADMIN"){
            $roles[] = 'ROLE_JURIST';    
            $roles[] = 'ROLE_ADMIN';    
        }

        if($this->getRole()=="JURIST"){
            $roles[] = 'ROLE_JURIST';    
        }

        if($this->getRole()=="DELETED"){
            $roles = [];    
        }

        return $roles;
    }

     /**
     * Get mdp
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->mdp;
    }
     
     /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->id;
    }
     
     /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->login;
    }

    public function eraseCredentials(){
        $this->plainPassword=null;
    }
}

