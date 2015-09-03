<?php

namespace rmatil\cms\Entities;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Exclude;
use rmatil\cms\Entities\BaseEntity;
use rmatil\cms\Entities\UserGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity 
 * @ORM\Table(name="users")
 **/
class User {

    /**
     * Id of the user
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
     * The usergroup to which the user belongs 
     *
     * @ORM\ManyToOne(targetEntity="UserGroup")
     *
     * @Type("rmatil\cms\Entities\UserGroup")
     * 
     * @var \rmatil\cms\Entities\UserGroup
     */
    protected $userGroup;

    /**
     * Username of the user
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $userName;

    /**
     * Firstname of the user
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $firstName;

    /**
     * Lastname of the user
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $lastName;

    /**
     * Email of the user
     * 
     * @ORM\Column(type="string")
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $email;

    /**
     * Phone number of the user
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @type("string")
     * 
     * @var string
     */
    protected $phoneNumber;

    /**
     * Mobile number of the user
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @type("string")
     * 
     * @var string
     */
    protected $mobileNumber;

    /**
     * Address of the user
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @type("string")
     * 
     * @var string
     */
    protected $address;

    /**
     * Zip code of the place
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @type("string")
     * 
     * @var string
     */
    protected $zipCode;

    /**
     * Place where user is inhabitant
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $place;

    /**
     * Plain password used for REST
     * 
     * @ORM\Column(type="string", nullable=true)
     *
     * @Type("string")
     * 
     * @var string
     */
    protected $plainPassword;

    /**
     * Hash of the users password including the salt
     * 
     * @ORM\Column(type="string", nullable=true)
     *
     * @Exclude
     * 
     * @var string
     */
    protected $passwordHash;

    /**
     * DateTime object of the users last login date.
     * May be null
     * 
     * @ORM\Column(type="datetime")
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     * 
     * @var \DateTime
     */
    protected $lastLoginDate;

    /**
     * DateTime object of the users registration date.
     * May be null
     * 
     * @ORM\Column(type="datetime")
     *
     * @Type("DateTime<'Y-m-d\TH:i:sP', 'UTC'>")
     * 
     * @var \DateTime
     */
    protected $registrationDate;

    /**
     * Indicates whether the user is locked or not
     * 
     * @ORM\Column(type="boolean")
     *
     * @Type("boolean")
     * 
     * @var boolean
     */
    protected $isLocked;

    /**
     * Indicates whether the user has validated his email or not
     * 
     * @ORM\Column(type="boolean")
     *
     * @Type("boolean")
     * 
     * @var boolean
     */
    protected $hasEmailValidated;

    /**
     * Indicates whether this article is locked 
     * for editing or not
     * 
     * @ORM\ManyToOne(targetEntity="User")
     *
     * @Type("rmatil\cms\Entities\User")
     * 
     * @var \rmatil\cms\Entities\User
     */
    protected $isLockedBy;


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
     * Gets the The usergroup to which the user belongs.
     *
     * @return \rmatil\cms\Entities\UserGroup
     */
    public function getUserGroup() {
        return $this->userGroup;
    }

    /**
     * Sets the The usergroup to which the user belongs.
     *
     * @param \rmatil\cms\Entities\UserGroup $userGroup the user group
     */
    public function setUserGroup(UserGroup $userGroup = null) {
        $this->userGroup = $userGroup;
    }

    /**
     * Gets the Username of the user.
     *
     * @return string
     */
    public function getUserName() {
        return $this->userName;
    }

    /**
     * Sets the Username of the user.
     *
     * @param string $userName the user name
     */
    public function setUserName($userName) {
        $this->userName = $userName;
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
     * Gets the Email of the user.
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Sets the Email of the user.
     *
     * @param string $email the email
     */
    public function setEmail($email) {
        $this->email = $email;
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

     /**
     * Gets the Plain password used for REST.
     *
     * @return string
     */
    public function getPlainPassword() {
        return $this->plainPassword;
    }

    /**
     * Sets the Plain password used for REST.
     *
     * @param string $plainPassword the plain password
     */
    public function setPlainPassword($plainPassword) {
        $this->plainPassword = $plainPassword;
    }

    /**
     * Gets the Hash of the users password.
     *
     * @return string
     */
    public function getPasswordHash() {
        return $this->passwordHash;
    }

    /**
     * Sets the Hash of the users password.
     *
     * @param string $passwordHash the password hash
     */
    public function setPasswordHash($passwordHash) {
        $this->passwordHash = $passwordHash;
    }

    /**
     * Gets the DateTime object of the users last login date. May be null.
     *
     * @return \DateTime
     */
    public function getLastLoginDate() {
        return $this->lastLoginDate;
    }

    /**
     * Sets the DateTime object of the users last login date. May be null.
     *
     * @param \DateTime $lastLoginDate the last login date
     */
    public function setLastLoginDate(\DateTime $lastLoginDate = null) {
        $this->lastLoginDate = $lastLoginDate;
    }

    /**
     * Gets the DateTime object of the users registration date. May be null.
     *
     * @return \DateTime
     */
    public function getRegistrationDate() {
        return $this->registrationDate;
    }

    /**
     * Sets the DateTime object of the users registration date. May be null.
     *
     * @param \DateTime $registrationDate the registration date
     */
    public function setRegistrationDate(\DateTime $registrationDate = null) {
        $this->registrationDate = $registrationDate;
    }

    /**
     * Gets the Indicates whether the user is locked or not.
     *
     * @return boolean
     */
    public function getIsLocked() {
        return $this->isLocked;
    }

    /**
     * Sets the Indicates whether the user is locked or not.
     *
     * @param boolean $isLocked the is locked
     */
    public function setIsLocked($isLocked) {
        $this->isLocked = $isLocked;
    }

    /**
     * Gets the Indicates whether the user has validated his email or not.
     *
     * @return boolean
     */
    public function getHasEmailValidated() {
        return $this->hasEmailValidated;
    }

    /**
     * Sets the Indicates whether the user has validated his email or not.
     *
     * @param boolean $hasEmailValidated the has email validated
     */
    public function setHasEmailValidated($hasEmailValidated) {
        $this->hasEmailValidated = $hasEmailValidated;
    }

    /**
     * Gets the user which locks this user
     *
     * @return boolean
     */
    public function getIsLockedBy() {
        return $this->isLockedBy;
    }

    /**
     * Sets the user which locks this article
     *
     * @param \rmatil\cms\Entities\User $isLockedBy the user which locks the article
     */
    public function setIsLockedBy(User $isLockedBy = null) {
        $this->isLockedBy = $isLockedBy;
    }

    public function update(User $user) {
        $this->setUserGroup($user->getUserGroup());
        $this->setUserName($user->getUserName());
        $this->setFirstName($user->getFirstName());
        $this->setLastName($user->getLastName());
        $this->setEmail($user->getEmail());
        $this->setPhoneNumber($user->getPhoneNumber());
        $this->setMobileNumber($user->getMobileNumber());
        $this->setAddress($user->getAddress());
        $this->setZipCode($user->getZipCode());
        $this->setPlace($user->getPlace());
        $this->setPlainPassword($user->getPlainPassword());
        $this->setPasswordHash($user->getPasswordHash());
        $this->setLastLoginDate($user->getLastLoginDate());
        $this->setRegistrationDate($user->getRegistrationDate());
        $this->setIsLocked($user->getIsLocked());
        $this->setIsLockedBy($user->getIsLockedBy());
    }
}