<?php

namespace rmatil\CmsBundle\Entity;

use JMS\Serializer\Annotation\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity 
 * @ORM\Table(name="repeatOptions")
 **/
class RepeatOption {

    const DAILY = "daily";

    const WEEKLY = "weekly";

    const YEARLY = "yearly";

    /**
     * Id of the RepeatOption
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
     * Repeat optionValue
     *
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $optionValue;


    /**
     * Gets the Id of the RepeatOption.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the Id of the RepeatOption.
     *
     * @param integer $id the id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the Repeat optionValue
     *
     * @return integer
     */
    public function getOption() {
        return $this->optionValue;
    }

    /**
     * Sets the Repeat optionValue
     *
     * @param string $optionValue the option
     */
    public function setOption($optionValue) {
        $this->optionValue = $optionValue;
    }

    public function update(RepeatOption $repeatOption) {
        $this->setOption($repeatOption->getOption());
    }

}
