<?php

namespace rmatil\cms\Entities;

use JMS\Serializer\Annotation\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity 
 * @ORM\Table(name="fileCategories")
 **/
class FileCategory {

    /**
     * Id of the category
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
     * Name of the category
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $name;
    

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

    public function update(FileCategory $fileCategory) {
        $this->setName($fileCategory->getName());
    }
}