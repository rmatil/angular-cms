<?php

namespace rmatil\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Attribute override is required by the indexes
 * See issue https://github.com/FriendsOfSymfony/FOSUserBundle/issues/1919
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="usernameCanonical",
 *          column=@ORM\Column(
 *              name     = "username_canonical",
 *              length   = 191,
 *              unique   = true
 *          )
 *      ),
 *      @ORM\AttributeOverride(name="emailCanonical",
 *          column=@ORM\Column(
 *              name     = "email_canonical",
 *              length   = 191,
 *              unique   = true
 *          )
 *      )
 * })
 **/
class User extends BaseUser {

    /**
     * Id of the user
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var integer
     */
    protected $id;

    /**
     * Firstname of the user
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $firstName = '';

    /**
     * Lastname of the user
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $lastName = '';

    /**
     * Phone number of the user
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $phoneNumber;

    /**
     * Mobile number of the user
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $mobileNumber;

    /**
     * Address of the user
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $address;

    /**
     * Zip code of the place
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $zipCode;

    /**
     * Place where user is inhabitant
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $place;

    public function __construct() {
        parent::__construct();
    }

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
     * Gets the Firstname of the user.
     *
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * Sets the Firstname of the user.
     *
     * @param string $firstName the first name
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    /**
     * Gets the Lastname of the user.
     *
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * Sets the Lastname of the user.
     *
     * @param string $lastName the last name
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    /**
     * Gets the Phone number of the user.
     *
     * @return string
     */
    public function getPhoneNumber() {
        return $this->phoneNumber;
    }

    /**
     * Sets the Phone number of the user.
     *
     * @param string $phoneNumber the phone number
     */
    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Gets the Mobile number of the user.
     *
     * @return string
     */
    public function getMobileNumber() {
        return $this->mobileNumber;
    }

    /**
     * Sets the Mobile number of the user.
     *
     * @param string $mobileNumber the mobile number
     */
    public function setMobileNumber($mobileNumber) {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * Gets the Address of the user.
     *
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Sets the Address of the user.
     *
     * @param string $address the address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * Gets the Zip code of the place.
     *
     * @return string
     */
    public function getZipCode() {
        return $this->zipCode;
    }

    /**
     * Sets the Zip code of the place.
     *
     * @param string $zipCode the zip code
     */
    public function setZipCode($zipCode) {
        $this->zipCode = $zipCode;
    }

    /**
     * Gets the Place where user is inhabitant.
     *
     * @return string
     */
    public function getPlace() {
        return $this->place;
    }

    /**
     * Sets the Place where user is inhabitant.
     *
     * @param string $place the place
     */
    public function setPlace($place) {
        $this->place = $place;
    }

    public function update(User $user) {
        $this->setUsername($user->getUsername());
        $this->setFirstName($user->getFirstName());
        $this->setLastName($user->getLastName());
        $this->setEmail($user->getEmail());
        $this->setPhoneNumber($user->getPhoneNumber());
        $this->setMobileNumber($user->getMobileNumber());
        $this->setAddress($user->getAddress());
        $this->setZipCode($user->getZipCode());
        $this->setPlace($user->getPlace());
    }
}
