<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="Entity\Points\Repository\UserRepository")
 * @ORM\Table(name="webpoints_point")
 */
class Point {

    const ENABLED = 1;
    const DISABLED = 0;

    /**
     * @ORM\Id @ORM\Column(type="integer",name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /** @ORM\Column(type="datetime",name="created",nullable=false) */
    protected $created;

    /** @ORM\Column(type="datetime",name="updated",nullable=false) */
    protected $updated;

    /** @ORM\Column(type="decimal",name="latitude",precision=12,scale=4,nullable=false) */
    protected $latitude;

    /** @ORM\Column(type="decimal",name="longitude",precision=12,scale=4,nullable=false) */
    protected $longitude;




    /**
     * @ORM\ManyToOne(targetEntity="\Entity\User", cascade={"all"}, fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;


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
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     * @return Point
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     * @return Point
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return Point
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }



}
