<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permission
 *
 * @ORM\Table(name="permission")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PermissionRepository")
 */
class Permission
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
     * @ORM\Column(name="contexte", type="string", length=100)
     */
    private $contexte;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=60)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="PermissionRole", mappedBy="permission")
     */
    protected $permissionRoles;


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
     * Set contexte
     *
     * @param string $contexte
     *
     * @return Permission
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

    /**
     * Set label
     *
     * @param string $label
     *
     * @return Permission
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
     * Set code
     *
     * @param string $code
     *
     * @return Permission
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->permissionRoles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add permissionRole
     *
     * @param \AppBundle\Entity\PermissionRole $permissionRole
     *
     * @return Permission
     */
    public function addPermissionRole(\AppBundle\Entity\PermissionRole $permissionRole)
    {
        $this->permissionRoles[] = $permissionRole;

        return $this;
    }

    /**
     * Remove permissionRole
     *
     * @param \AppBundle\Entity\PermissionRole $permissionRole
     */
    public function removePermissionRole(\AppBundle\Entity\PermissionRole $permissionRole)
    {
        $this->permissionRoles->removeElement($permissionRole);
    }

    /**
     * Get permissionRoles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPermissionRoles()
    {
        return $this->permissionRoles;
    }

    public function checkPermissionRole($role='SPECTATOR')
    {
        foreach ($this->permissionRoles as $permissionRole) {
            if($permissionRole->getRole()==$role){
                return true;
            }
        }
        return false;
    }
}
