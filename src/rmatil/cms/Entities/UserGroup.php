<?php

namespace rmatil\cms\Entities;

use JMS\Serializer\Annotation\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity 
 * @ORM\Table(name="userGroups")
 **/
class UserGroup {

    /**
     * Id of the user
     * 
     * @ORM\Id 
     * @ORM\Column(type="integer") 
     * @ORM\GeneratedValue
     *
     * @Type("integer")
     * 
     * @var integer
     */
    protected $id;

    /**
     * Name of the usergroup
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $name;

    /**
     * Role of the usergroup
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $role;

    
     /**
     * Gets the Id of the user.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the Id of the user.
     *
     * @param integer $id the id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the Name of the usergroup.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the Name of the usergroup.
     *
     * @param string $name the name
     */
    public function setName($name) {
        $this->name = $name;
    }

     /**
     * Gets the Role of the usergroup.
     *
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * Sets the Role of the usergroup.
     *
     * @param string $role the role
     */
    public function setRole($role) {
        $this->role = $role;
    }

    public function update(UserGroup $userGroup) {
        $this->setName($userGroup->getName());
        $this->setRole($userGroup->getRole());
    }
}