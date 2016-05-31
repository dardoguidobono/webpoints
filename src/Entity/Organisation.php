<?php
namespace Entity\Catalog;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Orm\DoctrineConnection;

/**
 * @ORM\Entity(repositoryClass="Entity\Catalog\Repository\OrganisationRepository")
 * @ORM\Table(name="organisation",indexes={@Index(name="legacy_code_idx", columns={"legacy_code"}),@Index(name="name_idx", columns={"name"})})
 */
class Organisation
{


    /**
     * @ORM\Id @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string",length=80,name="name",nullable=false) */
    protected $name;

    /** @ORM\Column(type="string",length=80,name="vat_number",nullable=false) */
    protected $vatNumber;

    /** @ORM\Column(type="datetime",name="created",nullable=false) */
    protected $created;

    /** @ORM\Column(type="datetime",name="updated",nullable=false) */
    protected $updated;

    /** @ORM\Column(type="string",length=255,name="address",nullable=true) */
    protected $address;

    /** @ORM\Column(type="string",length=80,name="legacy_code",nullable=false) */
    protected $legacyCode;

    /** @ORM\Column(type="string",length=80,name="email",nullable=false) */
    protected $email;

    /** @ORM\Column(type="string",length=80,name="zone",nullable=false) */
    protected $zone;

    /** @ORM\Column(type="string",length=80,name="city",nullable=false) */
    protected $city;

    /** @ORM\Column(type="string",length=80,name="state",nullable=false) */
    protected $state;

    /** @ORM\Column(type="string",length=80,name="cell_phone",nullable=false) */
    protected $cellPhone;

    /** @ORM\Column(type="string",length=80,name="grade",nullable=false) */
    protected $grade;

    /** @ORM\Column(type="string",length=80,name="category",nullable=false) */
    protected $category;





    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    /**
     * @param mixed $vatNumber
     */
    public function setVatNumber($vatNumber)
    {
        $this->vatNumber = $vatNumber;
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
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getLegacyCode()
    {
        return $this->legacyCode;
    }

    /**
     * @param string $legacyCode
     */
    public function setLegacyCode($legacyCode)
    {
        $this->legacyCode = $legacyCode;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param string $zone
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCellPhone()
    {
        return $this->cellPhone;
    }

    /**
     * @param string $cellPhone
     */
    public function setCellPhone($cellPhone)
    {
        $this->cellPhone = $cellPhone;
    }

    /**
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param string $grade
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * returns true if has movement file
     * @return bool
     */
    public function hasAccountMovements(){
        return file_exists( $this->getAccountMovementsFile() );
    }

    /**
     * get account movements file
     * @return bool
     */
    public function getAccountMovementsFile(){
        return "../cache/movimientos/{$this->getVatNumber()}.json";
    }

    /**
     * @return \StdClass stdclass version
     */
    public function toJson(){
        $obj = new \StdClass();
        $obj->id = $this->getId();
        $obj->value = $this->getName();
        return $obj;
    }

    /**
     * Return all valid emails addresses
     * @return array
     */
    public function getAllValidEmails(){
        $emails = [];
        if (filter_var( $this->getEmail(), FILTER_VALIDATE_EMAIL)){
            $emails[] = $this->getEmail();
        }
        $em = DoctrineConnection::get();
        $qb = $em->createQueryBuilder();
        $query = $qb->select('partial u.{id,email}')
                ->from('Entity\Catalog\User', 'u')
                ->where("u.organisation = :org")
                ->setParameter( "org", $this->getId() )
                ->getQuery();
        foreach( $query->iterate() as $userResult ){
            /** @var User $user */
            $user = $userResult[0];
            if (filter_var( $user->getEmail(), FILTER_VALIDATE_EMAIL)){
                if ( !in_array($user->getEmail(), $emails) ){
                    $emails[] = $user->getEmail();
                }
            }
        }
        return $emails;
    }

}