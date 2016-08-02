<?php


namespace rmatil\CmsBundle\Model;


use JMS\Serializer\Annotation\Type;

class PageCategoryDTO {

    /**
     * Id of the page category
     *
     * @Type("integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * Name of the category
     *
     * @Type("string")
     *
     * @var string
     */
    protected $name;

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
}
