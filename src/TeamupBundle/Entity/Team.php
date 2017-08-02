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
     * @ORM\Column(name="description", type="text", length=300, nullable=true)
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
     * @var \DateTime
     *
     * @ORM\Column(name="applied", type="datetime", nullable=true)
     */
    private $applied;

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
        if($status == 2)
        {
            $this->applied = new \DateTime();
        }
        else
        {
            $this->applied = null;
        }
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

    /**
     * Get interests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInterestsArray()
    {
        $interests = array();
        foreach ($this->users as $user) 
        {
            foreach ($user->getInterests() as $interest) 
            {
                array_push($interests, $interest->getName());
            }
        }
        $interests = array_unique($interests);
        return $interests;
    }

    /**
     * Get match score (how much this fits to current user/team)
     *
     * @return integer 
     */
    public function getMatchScore($currentUser)
    {
        if($currentUser->hasTeam())
        {
            return $this->getMatchScoreTeam($currentUser->getTeam());
        }

        $score = 0;

        $profile_team_score = 0;

        foreach ($this->getNeededs() as $needed) 
        {
            if($currentUser->getProfile()->getId() == $needed->getProfile()->getId())
                $profile_team_score = 1;
        }

        $interests_team_score = 0;

        $this_team_interests = array();

        foreach ($this->getUsers() as $user) 
        {
            foreach ($user->getInterests() as $interest) 
            {
                array_push($this_team_interests,$interest->getId());
            }
        }

        $currentUser_interests = array();

        foreach ($currentUser->getInterests() as $interest) 
        {
            array_push($currentUser_interests,$interest->getId());
        }

        $interests_team_score = floatval(count(array_intersect(array_unique($currentUser_interests),$this_team_interests)))*100/floatval(count(array_unique($currentUser_interests)));

        //ponderar puntaje
        $score = ($profile_team_score+$interests_team_score)/2;

        return $score;
    }

    /**
     * Get match score (how much this fits to currentTeam)
     *
     * @return integer 
     */
    public function getMatchScoreTeam($currentTeam)
    {
        $score = 0;

        $profile_team_score = 0;

        $count = 0;

        foreach ($currentTeam->getNeededs() as $needed) 
        {
            foreach ($this->getUsers() as $user) 
            {
                if($user->getProfile()->getId() == $needed->getProfile()->getId())
                    $profile_team_score += 1;
            }
            $count += $needed->getQuantity();
        }

        if($count == 0)
        {
            $profile_team_score = 0;
        }
        else
        {
            $profile_team_score = floatval($profile_team_score) / floatval($count);
        }

        $interests_team_score = 0;

        $this_team_interests = array();

        foreach ($this->getUsers() as $user) 
        {
            foreach ($user->getInterests() as $interest) 
            {
                array_push($this_team_interests,$interest->getId());
            }
        }

        $currentTeam_interests = array();

        foreach ($currentTeam->getUsers() as $user) 
        {
            foreach ($user->getInterests() as $interest) 
            {
                array_push($currentTeam_interests,$interest->getId());
            }
        }

        $interests_team_score = floatval(count(array_intersect(array_unique($currentTeam_interests),$this_team_interests)))*100/floatval(count(array_unique($currentTeam_interests)));

        //ponderar puntaje
        $score = ($profile_team_score+$interests_team_score)/2;

        return $score;
    }

    /**
     * Set applied
     *
     * @param \DateTime $applied
     * @return Team
     */
    public function setApplied($applied)
    {
        $this->applied = $applied;

        return $this;
    }

    /**
     * Get applied
     *
     * @return \DateTime 
     */
    public function getApplied()
    {
        return $this->applied;
    }
}
