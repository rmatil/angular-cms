<?php

namespace rmatil\CmsBundle\Entity;

use JMS\Serializer\Annotation\Type;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="settings")
 **/
class Setting {

    /**
     * Id of the setting
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
     * Name for this setting
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $name;

    /**
     * Value for this setting
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $value;


    /**
     * Gets the Id of the setting.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the Id of the setting.
     *
     * @param integer $id the id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the Name for this setting.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the Name for this setting.
     *
     * @param string $name the name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the Value for this setting.
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets the Value for this setting.
     *
     * @param string $value the value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    public function update(Setting $setting) {
        $this->setName($setting->getName());
        $this->setValue($setting->getValue());
    }
}
