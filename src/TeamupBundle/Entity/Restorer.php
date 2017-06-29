<?php

namespace TeamupBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Restorer
 *
 * @ORM\Table(name="restorer")
 * @ORM\Entity(repositoryClass="TeamupBundle\Repository\RestorerRepository")
 */
class Restorer
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
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="auth", type="string", length=60)
     */
    private $auth;

    /**
    * @ORM\OneToOne(targetEntity="User", inversedBy="restorer")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    */
    private $user;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Restorer
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set auth
     *
     * @param string $auth
     *
     * @return Restorer
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Get auth
     *
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Set user
     *
     * @param \TeamupBundle\Entity\User $user
     *
     * @return Restorer
     */
    public function setUser(\TeamupBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \TeamupBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
