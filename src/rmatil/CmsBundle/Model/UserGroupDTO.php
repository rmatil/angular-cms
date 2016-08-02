<?php


namespace rmatil\CmsBundle\Model;


use JMS\Serializer\Annotation\Type;

class UserGroupDTO {

    /**
     * Id of the user
     *
     * @Type("integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * Name of the usergroup
     *
     * @Type("string")
     *
     * @var string
     */
    protected $name;

    /**
     * Role of the usergroup
     *
     * @Type("string")
     *
     * @var string
     */
    protected $role;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole($role) {
        $this->role = $role;
    }
}
