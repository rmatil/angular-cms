<?php

namespace rmatil\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pageCategories")
 **/
class PageCategory {

    /**
     * Id of the page category
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var integer
     */
    protected $id;

    /**
     * Name of the category
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;


    /**
     * Gets the Id of the page category.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the Id of the page category.
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

    public function update(PageCategory $pageCategory) {
        $this->setName($pageCategory->getName());
    }

}
