<?php

namespace TeamupBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="TeamupBundle\Repository\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=50)
     */
    private $lastname;

    /**
     * @var int
     *
     * @ORM\Column(name="rut", type="integer", unique=true)
     */
    private $rut;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="brief", type="string", length=255, nullable=true)
     */
    private $brief;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=20)
     */
    private $role;

    /**
     * @Assert\Length(
     *      min = "5",
     *      max = "12",
     *      minMessage = "Contrase�a debe ser de almenos 5 caracteres",
     *      maxMessage = "Contrase�a debe ser de menos de 12 caracteres",
     *      groups = {"Default"}
     * )
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * Many Users have One Team.
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="users")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;

    /**
     * One User has Many Interest.
     * @ORM\OneToMany(targetEntity="Interest", mappedBy="user")
     */
    private $interests;

    /**
    * @ORM\OneToOne(targetEntity="Restorer", mappedBy="user", cascade={"persist"})
    */
    private $restorer;

    /**
     * One User has One Profile.
     * @ORM\OneToOne(targetEntity="Profile", mappedBy="user")
     */
    private $profile;

    public function __construct() {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));

        $this->interests = new ArrayCollection();
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        if (strlen($password) > 4) {
            $this->plainPassword = $password;
        }
        else
        {
            $this->plainPassword = $this->plainPassword;   
        }
    }

    public function setPassword($password)
    {
        if (strlen($password)>4) {
            $this->password = $password;
        }
        else
            $this->password = $this->password;

    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
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
     * @return User
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
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set rut
     *
     * @param integer $rut
     * @return User
     */
    public function setRut($rut)
    {
        $this->rut = $rut;

        return $this;
    }

    /**
     * Get rut
     *
     * @return integer 
     */
    public function getRut()
    {
        return $this->rut;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set brief
     *
     * @param string $brief
     * @return User
     */
    public function setBrief($brief)
    {
        $this->brief = $brief;

        return $this;
    }

    /**
     * Get brief
     *
     * @return string 
     */
    public function getBrief()
    {
        return $this->brief;
    }

    /**
     * Get verifier digit
     *
     * @return string
     */
    public function getVerifierDigit() 
    {
        $rut = $this->getRut();
        $nRut = (string)$rut;
        while($nRut[0] == "0") {
            $nRut = substr($nRut, 1);
        }
        $factor = 2;
        $suma = 0;
        for($i = strlen($nRut) - 1; $i >= 0; $i--) {
            $suma += $factor * $nRut[$i];
            $factor = $factor % 7 == 0 ? 2 : $factor + 1;
        }
        $dv = 11 - $suma % 11;
        /* Por alguna raz�n me daba que 11 % 11 = 11. Esto lo resuelve. */
        $dv = $dv == 11 ? 0 : ($dv == 10 ? "K" : $dv);
        return $dv;
    }

    public function isAccountNonExpired() 
    {        
        return true;
    }
    public function isAccountNonLocked() 
    {        
        return true;
    }
    public function isCredentialsNonExpired() 
    {        
        return true;
    }
    public function getUsername()
    {
        return $this->getEmail();
    }

    public function isEnabled() 
    {
        $active = $this->getIsActive();
        if (is_null($active))
            return true;
        else
            return $active;
    }

    /**
     * Set restorer
     *
     * @param \TeamupBundle\Entity\Restorer $restorer
     * @return User
     */
    public function setRestorer(\TeamupBundle\Entity\Restorer $restorer = null)
    {
        $this->restorer = $restorer;

        return $this;
    }

    /**
     * Get restorer
     *
     * @return \TeamupBundle\Entity\Restorer 
     */
    public function getRestorer()
    {
        return $this->restorer;
    }

    /**
     * Set team
     *
     * @param \TeamupBundle\Entity\Team $team
     * @return User
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
     * Add interests
     *
     * @param \TeamupBundle\Entity\Interest $interests
     * @return User
     */
    public function addInterest(\TeamupBundle\Entity\Interest $interests)
    {
        $this->interests[] = $interests;

        return $this;
    }

    /**
     * Remove interests
     *
     * @param \TeamupBundle\Entity\Interest $interests
     */
    public function removeInterest(\TeamupBundle\Entity\Interest $interests)
    {
        $this->interests->removeElement($interests);
    }

    /**
     * Get interests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInterests()
    {
        return $this->interests;
    }

    /**
     * Set profile
     *
     * @param \TeamupBundle\Entity\Profile $profile
     * @return User
     */
    public function setProfile(\TeamupBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;

        $profile->setUser($this);

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

    public function getRoles()
    {
        return array($this->role);
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }
    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
}