<?php

namespace rmatil\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="languages")
 **/
class Language {

    /**
     * Id of the language
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var integer
     */
    protected $id;

    /**
     * Name of the Language
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * ISO 639-1-Code of the Language: Is a 2-letter abbr.
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $code;


    /**
     * Gets the Id of the language.
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the Id of the language.
     *
     * @param integer $id the id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Gets the value of name.
     *
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param mixed $name the name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the ISO 639-1-Code of the Language: Is a 2-letter abbr..
     *
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Sets the ISO 639-1-Code of the Language: Is a 2-letter abbr..
     *
     * @param string $code the code
     */
    public function setCode($code) {
        $this->code = $code;
    }

    public function update(Language $language) {
        $this->setName($language->getName());
        $this->setCode($language->getCode());
    }
}
