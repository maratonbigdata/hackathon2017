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
     * @ORM\Column(name="rut", type="integer", unique=true, nullable=true)
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
     * @ORM\Column(name="brief", type="text", length=300, nullable=true)
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
     *      minMessage = "Contraseña debe ser de almenos 5 caracteres",
     *      maxMessage = "Contraseña debe ser de menos de 12 caracteres",
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
     * Many Users has Many Interest.
     * @ORM\ManyToMany(targetEntity="Interest", inversedBy="users")
     * @ORM\JoinTable(name="users_interests")
     */
    private $interests;

    /**
    * @ORM\OneToOne(targetEntity="Restorer", mappedBy="user", cascade={"persist"})
    */
    private $restorer;

    /**
     * Many Users have One Profile.
     * @ORM\ManyToOne(targetEntity="Profile", inversedBy="users")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     */
    private $profile;

    /**
     * One User have Many Messages.
     * @ORM\OneToMany(targetEntity="Message", mappedBy="sender")
     */
    private $sendedMessages;

    /**
     * One Users have Many Messages.
     * @ORM\OneToMany(targetEntity="Message", mappedBy="reciever")
     */
    private $recievedMessages;

    /**
     * One User sent Many Invitations.
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="sender")
     */
    private $sendedInvitations;

    /**
     * One User recieved Many Invitations.
     * @ORM\OneToMany(targetEntity="Invitation", mappedBy="reciever")
     */
    private $recievedInvitations;

    /**
     * One User sent Many Petitions.
     * @ORM\OneToMany(targetEntity="Petition", mappedBy="sender")
     */
    private $sendedPetitions;

    /**
     * One User recieved Many Petitions.
     * @ORM\OneToMany(targetEntity="Petition", mappedBy="reciever")
     */
    private $recievedPetitions;

    /**
     * @var string
     *
     * @ORM\Column(name="occupation", type="string", length=200)
     */
    private $occupation;

    /**
     * @var string
     *
     * @ORM\Column(name="university", type="string", length=200, nullable=true)
     */
    private $university;

    /**
     * @var string
     *
     * @ORM\Column(name="carrer", type="string", length=200, nullable=true)
     */
    private $carrer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="signed", type="datetime")
     */
    private $signed;

    public function __construct() {
        $this->isActive = true;
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid(null, true));

        $this->interests = new ArrayCollection();
        $this->sendedMessages = new ArrayCollection();
        $this->recievedMessages = new ArrayCollection();
        $this->sendedInvitations = new ArrayCollection();
        $this->recievedInvitations = new ArrayCollection();
        $this->sendedPetitions = new ArrayCollection();
        $this->recievedPetitions = new ArrayCollection();
        $this->signed = new \DateTime();
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
     * Get full name
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->name." ".$this->lastname;
    }

    /**
     * Get initials
     *
     * @return string 
     */
    public function getInitials()
    {
        return strtoupper(substr($this->name,0,1).substr($this->lastname,0,1));
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
        /* Por alguna razón me daba que 11 % 11 = 11. Esto lo resuelve. */
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
        $team->addUser($this);

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
     * Bool if has a team
     *
     * @return boolean
     */
    public function hasTeam()
    {
        if(is_null($this->team))
        {
            return false;
        }
        return true;
    }

    /**
     * Bool if has a team
     *
     * @return boolean
     */
    public function isSearching()
    {
        if(!is_null($this->team) && $this->team->getStatus() != 1)
        {
            return false;
        }
        return true;
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

        echo var_dump($interests);
        die();

        $interests->setUser($this);

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

        $interests->setUser(null);
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

    public function getRoles()
    {
        return array($this->role);
    }

    /**
     * Get match score
     *
     * @return integer 
     */
    public function getMatchScore($currentUser)
    {
        $score = 0;
        $team = $currentUser->getTeam();

        $profile_score = 0;

        foreach ($team->getNeededs() as $needed) 
        {
            if($this->getProfile()->getId() == $needed->getProfile()->getId())
                $profile_score = 100;
        }

        $interests_score = 0;

        $team_interests = array();

        foreach ($team->getUsers() as $user) 
        {
            foreach ($user->getInterests() as $interest) 
            {
                array_push($team_interests,$interest->getId());
            }
        }

        $user_interests = array();

        foreach ($this->getInterests() as $interest) 
        {
            array_push($user_interests, $interest->getId());
        }

        $interests_score = floatval(count(array_intersect(array_unique($team_interests),$user_interests)))*100/floatval(count(array_unique($team_interests)));

        //ponderar puntaje
        $score = ($profile_score+$interests_score)/2;

        return $score;
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

    /**
     * Get profile icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->profile->getIconText();
    }

    /**
     * Add sendedMessages
     *
     * @param \TeamupBundle\Entity\Message $sendedMessages
     * @return User
     */
    public function addSendedMessage(\TeamupBundle\Entity\Message $sendedMessages)
    {
        $this->sendedMessages[] = $sendedMessages;

        return $this;
    }

    /**
     * Remove sendedMessages
     *
     * @param \TeamupBundle\Entity\Message $sendedMessages
     */
    public function removeSendedMessage(\TeamupBundle\Entity\Message $sendedMessages)
    {
        $this->sendedMessages->removeElement($sendedMessages);
    }

    /**
     * Get sendedMessages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSendedMessages()
    {
        return $this->sendedMessages;
    }

    /**
     * Add recievedMessages
     *
     * @param \TeamupBundle\Entity\Message $recievedMessages
     * @return User
     */
    public function addRecievedMessage(\TeamupBundle\Entity\Message $recievedMessages)
    {
        $this->recievedMessages[] = $recievedMessages;

        return $this;
    }

    /**
     * Remove recievedMessages
     *
     * @param \TeamupBundle\Entity\Message $recievedMessages
     */
    public function removeRecievedMessage(\TeamupBundle\Entity\Message $recievedMessages)
    {
        $this->recievedMessages->removeElement($recievedMessages);
    }

    /**
     * Get recievedMessages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecievedMessages()
    {
        return $this->recievedMessages;
    }

    /**
     * Add sendedInvitations
     *
     * @param \TeamupBundle\Entity\Invitation $sendedInvitations
     * @return User
     */
    public function addSendedInvitation(\TeamupBundle\Entity\Invitation $sendedInvitations)
    {
        $this->sendedInvitations[] = $sendedInvitations;

        return $this;
    }

    /**
     * Remove sendedInvitations
     *
     * @param \TeamupBundle\Entity\Invitation $sendedInvitations
     */
    public function removeSendedInvitation(\TeamupBundle\Entity\Invitation $sendedInvitations)
    {
        $this->sendedInvitations->removeElement($sendedInvitations);
    }

    /**
     * Get sendedInvitations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSendedInvitations()
    {
        return $this->sendedInvitations;
    }

    /**
     * Add recievedInvitations
     *
     * @param \TeamupBundle\Entity\Invitation $recievedInvitations
     * @return User
     */
    public function addRecievedInvitation(\TeamupBundle\Entity\Invitation $recievedInvitations)
    {
        $this->recievedInvitations[] = $recievedInvitations;

        return $this;
    }

    /**
     * Remove recievedInvitations
     *
     * @param \TeamupBundle\Entity\Invitation $recievedInvitations
     */
    public function removeRecievedInvitation(\TeamupBundle\Entity\Invitation $recievedInvitations)
    {
        $this->recievedInvitations->removeElement($recievedInvitations);
    }

    /**
     * Get recievedInvitations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecievedInvitations()
    {
        return $this->recievedInvitations;
    }

    /**
     * Add sendedPetitions
     *
     * @param \TeamupBundle\Entity\Petition $sendedPetitions
     * @return User
     */
    public function addSendedPetition(\TeamupBundle\Entity\Petition $sendedPetitions)
    {
        $this->sendedPetitions[] = $sendedPetitions;

        return $this;
    }

    /**
     * Remove sendedPetitions
     *
     * @param \TeamupBundle\Entity\Petition $sendedPetitions
     */
    public function removeSendedPetition(\TeamupBundle\Entity\Petition $sendedPetitions)
    {
        $this->sendedPetitions->removeElement($sendedPetitions);
    }

    /**
     * Get sendedPetitions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSendedPetitions()
    {
        return $this->sendedPetitions;
    }

    /**
     * Add recievedPetitions
     *
     * @param \TeamupBundle\Entity\Petition $recievedPetitions
     * @return User
     */
    public function addRecievedPetition(\TeamupBundle\Entity\Petition $recievedPetitions)
    {
        $this->recievedPetitions[] = $recievedPetitions;

        return $this;
    }

    /**
     * Remove recievedPetitions
     *
     * @param \TeamupBundle\Entity\Petition $recievedPetitions
     */
    public function removeRecievedPetition(\TeamupBundle\Entity\Petition $recievedPetitions)
    {
        $this->recievedPetitions->removeElement($recievedPetitions);
    }

    /**
     * Get recievedPetitions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecievedPetitions()
    {
        return $this->recievedPetitions;
    }

    /**
     * Get recievedPetitions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function hasRecievedPetitions($user)
    {
        $has = false;

        foreach ($this->recievedPetitions as $petition) 
        {
            if($petition->getSender()->getId() == $user->getId())
                $has = true;
        }
        return $has;
    }

    /**
     * Get recievedInvitation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function hasRecievedInvitations($user)
    {
        $has = false;

        foreach ($this->recievedInvitations as $invitation) 
        {
            if($invitation->getSender()->getId() == $user->getId())
                $has = true;
        }
        return $has;
    }

    /**
     * Set occupation
     *
     * @param string $occupation
     * @return User
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;

        return $this;
    }

    /**
     * Get occupation
     *
     * @return string 
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Get occupation options
     *
     * @return array 
     */
    public function getOccupationOptions()
    {
        $options = array('Estudiante Pregrado', 'Estudiante Postgrados');

        return $options;
    }

    /**
     * Set university
     *
     * @param string $university
     * @return User
     */
    public function setUniversity($university)
    {
        $this->university = $university;

        return $this;
    }

    /**
     * Get university
     *
     * @return string 
     */
    public function getUniversity()
    {
        return $this->university;
    }

    /**
     * Get university options
     *
     * @return string 
     */
    public function getUniversityOptions()
    {
        $options = array(
            'CFT ALFA',
            'CFT ALPES',
            'CFT ANDRES BELLO',
            'CFT BARROS ARANA',
            'CFT CAMARA DE COMERCIO DE SANTIAGO',
            'CFT CENCO',
            'CFT CENTRO TECNOLOGICO SUPERIOR INFOMED',
            'CFT DE ENAC',
            'CFT DE ENSEÑANZA DE ALTA COSTURA PAULINA DIARD',
            'CFT DE LA INDUSTRIA GRAFICA - INGRAF',
            'CFT DE TARAPACA',
            'CFT DEL MEDIO AMBIENTE',
            'CFT EDUCAP',
            'CFT ESANE DEL NORTE',
            'CFT ESCUELA CULINARIA FRANCESA - ECOLE',
            'CFT ESCUELA DE ARTES APLICADAS OFICIOS DEL FUEGO',
            'CFT ESTUDIO PROFESOR VALERO',
            'CFT ICEL',
            'CFT INACAP',
            'CFT INSTITUTO CENTRAL DE CAPACITACION EDUCACIONAL ICCE',
            'CFT INSTITUTO SUPERIOR ALEMAN DE COMERCIO INSALCO',
            'CFT INSTITUTO SUPERIOR DE ESTUDIOS JURIDICOS CANON',
            'CFT INSTITUTO TECNOLOGICO DE CHILE - I.T.C.',
            'CFT IPROSEC',
            'CFT JUAN BOHON',
            'CFT LAPLACE',
            'CFT LOS LAGOS',
            'CFT LOTA-ARAUCO',
            'CFT LUIS ALBERTO VERA',
            'CFT MANPOWER',
            'CFT MASSACHUSETTS',
            'CFT PROANDES',
            'CFT PRODATA',
            'CFT PROFASOC',
            'CFT PROTEC',
            'CFT SAN AGUSTIN DE TALCA',
            'CFT SANTO TOMAS',
            'CFT TEODORO WICKEL KLUWEN',
            'CFT UCEVALPO',
            'IP AGRARIO ADOLFO MATTHEI',
            'IP AIEP',
            'IP CARLOS CASANUEVA',
            'IP CIISA',
            'IP CHILENO-BRITANICO DE CULTURA',
            'IP DE ARTE Y COMUNICACION ARCOS',
            'IP DE ARTES ESCENICAS KAREN CONNOLLY',
            'IP DE CIENCIAS DE LA COMPUTACION ACUARIO DATA',
            'IP DE CIENCIAS Y ARTES INCACEA',
            'IP DE CIENCIAS Y EDUCACION HELEN KELLER',
            'IP DE CHILE',
            'IP DEL COMERCIO',
            'IP DEL VALLE CENTRAL',
            'IP DIEGO PORTALES',
            'IP DR. VIRGINIO GOMEZ G.',
            'IP DUOC UC',
            'IP EATRI INSTITUTO PROFESIONAL',
            'IP ESCUELA DE CINE DE CHILE',
            'IP ESCUELA DE CONTADORES AUDITORES DE SANTIAGO',
            'IP ESCUELA MODERNA DE MUSICA',
            'IP ESUCOMEX',
            'IP INACAP',
            'IP INSTITUTO DE ESTUDIOS BANCARIOS GUILLERMO SUBERCASEAUX',
            'IP INSTITUTO INTERNACIONAL DE ARTES CULINARIAS Y SERVICIOS',
            'IP INSTITUTO NACIONAL DEL FUTBOL',
            'IP INSTITUTO SUPERIOR DE ARTES Y CIENCIAS DE LA COMUNICACION',
            'IP IPG',
            'IP LA ARAUCANA',
            'IP LATINOAMERICANO DE COMERCIO EXTERIOR',
            'IP LIBERTADOR DE LOS ANDES',
            'IP LOS LAGOS',
            'IP LOS LEONES',
            'IP MAR FUTURO',
            'IP PROJAZZ',
            'IP PROVIDENCIA',
            'IP SANTO TOMAS',
            'IP VERTICAL',
            'PONTIFICIA UNIVERSIDAD CATOLICA DE CHILE',
            'PONTIFICIA UNIVERSIDAD CATOLICA DE VALPARAISO',
            'UNIVERSIDAD ACADEMIA DE HUMANISMO CRISTIANO',
            'UNIVERSIDAD ADOLFO IBAÑEZ',
            'UNIVERSIDAD ADVENTISTA DE CHILE',
            'UNIVERSIDAD ALBERTO HURTADO',
            'UNIVERSIDAD ANDRES BELLO',
            'UNIVERSIDAD ARTURO PRAT',
            'UNIVERSIDAD AUSTRAL DE CHILE',
            'UNIVERSIDAD AUTONOMA DE CHILE',
            'UNIVERSIDAD BERNARDO OHIGGINS',
            'UNIVERSIDAD BOLIVARIANA',
            'UNIVERSIDAD CATOLICA DE LA SANTISIMA CONCEPCION',
            'UNIVERSIDAD CATOLICA DE TEMUCO',
            'UNIVERSIDAD CATOLICA DEL MAULE',
            'UNIVERSIDAD CATOLICA DEL NORTE',
            'UNIVERSIDAD CATOLICA SILVA HENRIQUEZ',
            'UNIVERSIDAD CENTRAL DE CHILE',
            'UNIVERSIDAD CHILENO BRITANICA DE CULTURA',
            'UNIVERSIDAD DE ACONCAGUA',
            'UNIVERSIDAD DE ANTOFAGASTA',
            'UNIVERSIDAD DE ARTE Y CIENCIAS SOCIALES ARCIS',
            'UNIVERSIDAD DE ARTES, CIENCIAS Y COMUNICACION - UNIACC',
            'UNIVERSIDAD DE ATACAMA',
            'UNIVERSIDAD DE AYSEN',
            'UNIVERSIDAD DE CONCEPCION',
            'UNIVERSIDAD DE CHILE',
            'UNIVERSIDAD DE LA FRONTERA',
            'UNIVERSIDAD DE LA SERENA',
            'UNIVERSIDAD DE LAS AMERICAS',
            'UNIVERSIDAD DE LOS ANDES',
            'UNIVERSIDAD DE LOS LAGOS',
            'UNIVERSIDAD DE MAGALLANES',
            'UNIVERSIDAD DE OHIGGINS',
            'UNIVERSIDAD DE PLAYA ANCHA DE CIENCIAS DE LA EDUCACION',
            'UNIVERSIDAD DE SANTIAGO DE CHILE',
            'UNIVERSIDAD DE TALCA',
            'UNIVERSIDAD DE TARAPACA',
            'UNIVERSIDAD DE VALPARAISO',
            'UNIVERSIDAD DE VIÑA DEL MAR',
            'UNIVERSIDAD DEL BIO-BIO',
            'UNIVERSIDAD DEL DESARROLLO',
            'UNIVERSIDAD DEL PACIFICO',
            'UNIVERSIDAD DIEGO PORTALES',
            'UNIVERSIDAD FINIS TERRAE',
            'UNIVERSIDAD GABRIELA MISTRAL',
            'UNIVERSIDAD IBEROAMERICANA DE CIENCIAS Y TECNOLOGIA, UNICYT',
            'UNIVERSIDAD LA REPUBLICA',
            'UNIVERSIDAD LOS LEONES',
            'UNIVERSIDAD MAYOR',
            'UNIVERSIDAD METROPOLITANA DE CIENCIAS DE LA EDUCACION',
            'UNIVERSIDAD MIGUEL DE CERVANTES',
            'UNIVERSIDAD PEDRO DE VALDIVIA',
            'UNIVERSIDAD SAN SEBASTIAN',
            'UNIVERSIDAD SANTO TOMAS',
            'UNIVERSIDAD SEK',
            'UNIVERSIDAD TECNICA FEDERICO SANTA MARIA',
            'UNIVERSIDAD TECNOLOGICA DE CHILE INACAP',
            'UNIVERSIDAD TECNOLOGICA METROPOLITANA',
            'UNIVERSIDAD UCINF'
            );

        return $options;
    }

    /**
     * Set carrer
     *
     * @param string $carrer
     * @return User
     */
    public function setCarrer($carrer)
    {
        $this->carrer = $carrer;

        return $this;
    }

    /**
     * Get carrer
     *
     * @return string 
     */
    public function getCarrer()
    {
        return $this->carrer;
    }

    /**
     * Get carrer options
     *
     * @return string 
     */
    public function getCarrerOptions()
    {
        $options = aray(
            'Ingeniería Civil',
            'Ingeniería Comercial/Negocios',
            'Diseño',
            'Arquitectura',
            'Artes/Música/Teatro',
            'Ciencias',
            'Psicología',
            'Comunicaciones / Periodismo',
            'Sociología',
            'Derecho',
            'Educación',
            'Agronomía/Forestal',
            'Medicina',
            'Enfermería',
            'Kinesiología',
            'Construcción Civil',
            'Otros'
            );
        return $options;
    }

    /**
     * Set signed
     *
     * @param \DateTime $signed
     * @return User
     */
    public function setSigned($signed)
    {
        $this->signed = $signed;

        return $this;
    }

    /**
     * Get signed
     *
     * @return \DateTime 
     */
    public function getSigned()
    {
        return $this->signed;
    }
}
