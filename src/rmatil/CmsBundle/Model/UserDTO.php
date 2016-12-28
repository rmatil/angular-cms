<?php


namespace rmatil\CmsBundle\Model;


use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;

class UserDTO {

    /**
     * Id of the user
     *
     * @Type("integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * Firstname of the user
     *
     * @Type("string")
     *
     * @var string
     */
    protected $firstName = '';

    /**
     * Lastname of the user
     *
     * @Type("string")
     *
     * @var string
     */
    protected $lastName = '';

    /**
     * Phone number of the user
     *
     * @type("string")
     *
     * @var string
     */
    protected $phoneNumber;

    /**
     * Mobile number of the user
     *
     * @type("string")
     *
     * @var string
     */
    protected $mobileNumber;

    /**
     * Address of the user
     *
     * @type("string")
     *
     * @var string
     */
    protected $address;

    /**
     * Zip code of the place
     *
     * @type("string")
     *
     * @var string
     */
    protected $zipCode;

    /**
     * Place where user is inhabitant
     *
     * @Type("string")
     *
     * @var string
     */
    protected $place;


    /**
     * Email address
     *
     * @Type("string")
     *
     * @var string
     */
    protected $email = '';

    /**
     * Whether the user is locked
     *
     * @Type("boolean")
     *
     * @var bool
     */
    protected $isLocked;

    /**
     * Whether the user account is expired
     *
     * @Type("boolean")
     *
     * @var bool
     */
    protected $isExpired;

    /**
     * Whether the user account is enabled
     *
     * @Type("boolean")
     *
     * @var bool
     */
    protected $isEnabled;

    /**
     * The date of the last login
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     *
     * @var \DateTime
     */
    protected $lastLoginDate;

    /**
     * All roles of the user
     *
     * @Type("array<string>")
     *
     * @var array
     */
    protected $roles = [];

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
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber() {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $phoneNumber
     */
    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return mixed
     */
    public function getMobileNumber() {
        return $this->mobileNumber;
    }

    /**
     * @param mixed $mobileNumber
     */
    public function setMobileNumber($mobileNumber) {
        $this->mobileNumber = $mobileNumber;
    }

    /**
     * @return mixed
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getZipCode() {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     */
    public function setZipCode($zipCode) {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string
     */
    public function getPlace() {
        return $this->place;
    }

    /**
     * @param string $place
     */
    public function setPlace($place) {
        $this->place = $place;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return boolean
     */
    public function isLocked() {
        return $this->isLocked;
    }

    /**
     * @param boolean $isLocked
     */
    public function setIsLocked($isLocked) {
        $this->isLocked = $isLocked;
    }

    /**
     * @return boolean
     */
    public function isExpired() {
        return $this->isExpired;
    }

    /**
     * @param boolean $isExpired
     */
    public function setIsExpired($isExpired) {
        $this->isExpired = $isExpired;
    }

    /**
     * @return boolean
     */
    public function isEnabled() {
        return $this->isEnabled;
    }

    /**
     * @param boolean $isEnabled
     */
    public function setIsEnabled($isEnabled) {
        $this->isEnabled = $isEnabled;
    }

    /**
     * @return \DateTime
     */
    public function getLastLoginDate() {
        return $this->lastLoginDate;
    }

    /**
     * @param \DateTime $lastLoginDate
     */
    public function setLastLoginDate($lastLoginDate) {
        $this->lastLoginDate = $lastLoginDate;
    }

    /**
     * @return array
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles) {
        $this->roles = $roles;
    }
}
