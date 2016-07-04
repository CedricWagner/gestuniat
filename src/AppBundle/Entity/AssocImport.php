<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AssocImport
 *
 * @ORM\Table(name="assoc_import")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssocImportRepository")
 */
class AssocImport
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
     * @var int
     *
     * @ORM\Column(name="oldId", type="integer")
     */
    private $oldId;

    /**
     * @var int
     *
     * @ORM\Column(name="newId", type="integer")
     */
    private $newId;

    /**
     * @var string
     *
     * @ORM\Column(name="entity", type="string", length=100)
     */
    private $entity;

    /**
     * @var string
     *
     * @ORM\Column(name="meta", type="string", length=255, nullable=true)
     */
    private $meta;


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
     * Set oldId
     *
     * @param integer $oldId
     *
     * @return AssocImport
     */
    public function setOldId($oldId)
    {
        $this->oldId = $oldId;

        return $this;
    }

    /**
     * Get oldId
     *
     * @return int
     */
    public function getOldId()
    {
        return $this->oldId;
    }

    /**
     * Set newId
     *
     * @param integer $newId
     *
     * @return AssocImport
     */
    public function setNewId($newId)
    {
        $this->newId = $newId;

        return $this;
    }

    /**
     * Get newId
     *
     * @return int
     */
    public function getNewId()
    {
        return $this->newId;
    }

    /**
     * Set entity
     *
     * @param string $entity
     *
     * @return AssocImport
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set meta
     *
     * @param string $meta
     *
     * @return AssocImport
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get meta
     *
     * @return string
     */
    public function getMeta()
    {
        return $this->meta;
    }


}

