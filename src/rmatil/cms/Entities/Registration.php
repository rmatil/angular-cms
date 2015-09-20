<?php

namespace rmatil\cms\Entities;

use JMS\Serializer\Annotation\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity 
 * @ORM\Table(name="registrations")
 **/
class Registration {

    /**
     * Id of the registration
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
     * The user of this registration
     *
     * @ORM\ManyToOne(targetEntity="User")
     *
     * @Type("rmatil\cms\Entities\User")
     * 
     * @var \rmatil\cms\Entities\User
     */
    protected $user;

    /**
     * DateTime object of the expiry date
     * 
     * @ORM\Column(type="datetime")
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     * 
     * @var \DateTime
     */
    protected $expirationDate;

    /**
     * Token to registrate the user
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $token;

    

    /**
     * Gets the The user of this registration.
     *
     * @return \rmatil\cms\Entities\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Sets the The user of this registration.
     *
     * @param \rmatil\cms\Entities\User $user the user
     */
    public function setUser(User $user = null) {
        $this->user = $user;
    }

    /**
     * Gets the DateTime object of the expiry date.
     *
     * @return \DateTime
     */
    public function getExpirationDate() {
        return $this->expirationDate;
    }

    /**
     * Sets the DateTime object of the expiry date.
     *
     * @param \DateTime $expirationDate the expired
     */
    public function setExpirationDate(\DateTime $expirationDate = null) {
        $this->expirationDate = $expirationDate;
    }

    /**
     * Gets the Token to registrate the user.
     *
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Sets the Token to registrate the user.
     *
     * @param string $token the token
     */
    public function setToken($token) {
        $this->token = $token;
    }

    public function update(Registration $registration) {
        $this->setUser($registration->getUser());
        $this->setExpirationDate($registration->getExpirationDate());
        $this->setToken($registration->getToken());
    }
}