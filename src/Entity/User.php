<?php

namespace Entity\Catalog;

use Doctrine\ORM\Mapping as ORM;
use Orm\DoctrineConnection;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass="Entity\Catalog\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User implements UserInterface {

    const ENABLED = 1;
    const DISABLED = 0;

    /**
     * @ORM\Id @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string", length=80,name="username",nullable=false,unique=true) */
    protected $username;

    /** @ORM\Column(type="string", length=80,name="email",nullable=false) */
    protected $email;

    /** @ORM\Column(type="string", length=120,name="password",nullable=false) */
    protected $password;

    /** @ORM\Column(type="string", length=50,name="firstname",nullable=false) */
    protected $firstname;

    /** @ORM\Column(type="string", length=50,name="surname",nullable=false) */
    protected $surname;

    /** @ORM\Column(type="string", length=100,name="phone_identifier",nullable=true) */
    protected $phone_identifier;

    /** @ORM\Column(type="string", length=120,name="phone_notification_token",nullable=true) */
    protected $phone_notification_token;

    /** @ORM\Column(type="string", length=1,name="phone_platform",nullable=true) */
    protected $phone_platform;

    /** @ORM\Column(type="smallint",name="status",nullable=false) */
    protected $status;

    /** @ORM\Column(type="datetime",name="created",nullable=false) */
    protected $created;

    /** @ORM\Column(type="datetime",name="updated",nullable=false) */
    protected $updated;

    /** @ORM\Column(type="datetime",name="last_logon",nullable=true) */
    protected $last_logon;

    /** @var  string trasient plain password */
    protected $plainPassword;

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }


    /**
     * @ORM\ManyToOne(targetEntity="Organisation", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id")
     */
    protected $organisation;


    public function __construct() {

        $this->setCreated(new \DateTime('now'));
        $this->setUpdated(new \DateTime('now'));
        $this->setStatus( self::ENABLED );

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getPhoneIdentifier()
    {
        return $this->phone_identifier;
    }

    /**
     * @param mixed $phone_identifier
     */
    public function setPhoneIdentifier($phone_identifier)
    {
        $this->phone_identifier = $phone_identifier;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return mixed
     */
    public function getLastLogon()
    {
        return $this->last_logon;
    }

    /**
     * @param mixed $last_logon
     */
    public function setLastLogon($last_logon)
    {
        $this->last_logon = $last_logon;
    }

    /**
     * @return mixed
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * @param mixed $organisation
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPhoneNotificationToken()
    {
        return $this->phone_notification_token;
    }

    /**
     * @param mixed $phone_notification_token
     */
    public function setPhoneNotificationToken($phone_notification_token)
    {
        $this->phone_notification_token = $phone_notification_token;
    }

    /**
     * @return mixed
     */
    public function getPhonePlatform()
    {
        return $this->phone_platform;
    }

    /**
     * @param mixed $phone_platform
     */
    public function setPhonePlatform($phone_platform)
    {
        $this->phone_platform = $phone_platform;
    }



    /**
     * Returns the roles granted to the user.
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     *
     * @return boolean if has phone notification enabled
     */
    public function hasPhone(){
        return (! ($this->phone_identifier === null) );
    }

}
