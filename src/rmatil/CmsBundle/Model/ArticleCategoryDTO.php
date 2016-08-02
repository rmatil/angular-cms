<?php


namespace rmatil\CmsBundle\Model;


use JMS\Serializer\Annotation\Type;

class ArticleCategoryDTO {

    /**
     * Id of the article category
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
     * Gets the Id of the article category.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the Id of the article category.
     *
     * @param integer $id the id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the Name of the category.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the Name of the category.
     *
     * @param string $name the name
     */
    public function setName($name) {
        $this->name = $name;
    }

}
