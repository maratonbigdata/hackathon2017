<?php

namespace TeamupBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Needed
 *
 * @ORM\Table(name="needed")
 * @ORM\Entity(repositoryClass="TeamupBundle\Repository\NeededRepository")
 */
class Needed
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
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * Many Needs have One Team.
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="neededs", cascade={"persist"})
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;

    /**
     * Many Needed have One Profile.
     * @ORM\ManyToOne(targetEntity="Profile", cascade={"persist"})
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     */
    private $profile;

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
     * Set quantity
     *
     * @param integer $quantity
     * @return Needed
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set team
     *
     * @param \TeamupBundle\Entity\Team $team
     * @return Needed
     */
    public function setTeam(\TeamupBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \TeamupBundle\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set profile
     *
     * @param \TeamupBundle\Entity\Profile $profile
     * @return Needed
     */
    public function setProfile(\TeamupBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return \TeamupBundle\Entity\Profile 
     */
    public function getProfile()
    {
        return $this->profile;
    }
}
