<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PermissionRole
 *
 * @ORM\Table(name="permission_role")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PermissionRoleRepository")
 */
class PermissionRole
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
     * @ORM\Column(name="role", type="string", length=20)
     */
    private $role;


    /**
     * @ORM\ManyToOne(targetEntity="Permission", inversedBy="permissionRoles")
     * @ORM\JoinColumn(name="permission_id", referencedColumnName="id")
     */
    private $permission;


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
     * Set role
     *
     * @param string $role
     *
     * @return PermissionRole
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
     * Set permission
     *
     * @param \AppBundle\Entity\Permission $permission
     *
     * @return PermissionRole
     */
    public function setPermission(\AppBundle\Entity\Permission $permission = null)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return \AppBundle\Entity\Permission
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
