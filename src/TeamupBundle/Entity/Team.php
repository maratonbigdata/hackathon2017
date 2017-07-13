<?php

namespace TeamupBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="TeamupBundle\Repository\TeamRepository")
 */
class Team
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, unique=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime")
     */
    private $modified;

    /**
     * One team has many users
     * @ORM\OneToMany(targetEntity="User", mappedBy="team")
     */
    private $users;

    /**
     * One Team has Many Neededs.
     * @ORM\OneToMany(targetEntity="Needed", mappedBy="team", cascade={"persist"})
     */
    private $neededs;

    public function __construct() {
        $this->users = new ArrayCollection();
        $this->neededs = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Team
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get status text
     *
     * @return integer 
     */
    public function getStatusText()
    {
        switch ($this->status) 
        {
            case 1:
                return 'Buscando Miembros';
                break;

            case 2:
                return 'Postulado';
                break;

            case 3:
                return 'Aceptado';
                break;

            case 4:
                return 'No Aceptado';
                break;

            case 5:
                return 'Pendiente Confirmación Miembros ';
                break;

            default:
                return 'Buscando Miembros';
                break;
        }
        return $this->status;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Team
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Team
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Add users
     *
     * @param \TeamupBundle\Entity\User $user
     * @return Team
     */
    public function addUser(\TeamupBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \TeamupBundle\Entity\User $user
     */
    public function removeUser(\TeamupBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add neededs
     *
     * @param \TeamupBundle\Entity\Needed $neededs
     * @return Team
     */
    public function addNeeded(\TeamupBundle\Entity\Needed $neededs)
    {
        $this->neededs[] = $neededs;
        $neededs->setTeam($this);

        return $this;
    }

    /**
     * Remove neededs
     *
     * @param \TeamupBundle\Entity\Needed $neededs
     */
    public function removeNeeded(\TeamupBundle\Entity\Needed $neededs)
    {
        $neededs->setTeam(null);
        $this->neededs->removeElement($neededs);
    }

    /**
     * Get neededs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNeededs()
    {
        return $this->neededs;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Team
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}
