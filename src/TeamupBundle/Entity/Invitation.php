<?php

namespace TeamupBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invitation
 *
 * @ORM\Table(name="invitation")
 * @ORM\Entity(repositoryClass="TeamupBundle\Repository\InvitationRepository")
 */
class Invitation
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state;

    /**
     * Many Invitations have One Sender.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sendedInvitations")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     */
    private $sender;

    /**
     * Many Invitations have One Reciever.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="recievedInvitations")
     * @ORM\JoinColumn(name="reciever_id", referencedColumnName="id")
     */
    private $reciever;

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
     * Set date
     *
     * @param \DateTime $date
     * @return Invitation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Invitation
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get state text
     *
     * @return string 
     */
    public function getStateText()
    {
        switch ($this->state) 
        {
            case 1:
                return 'Enviada';
                break;

            case 2:
                return 'Aceptada';
                break;

            case 3:
                return 'Rechazada';
                break;
            
            case 4:
                return 'Re enviada';
                break;
            case 5:
                return 'Anulada, el grupo ya postuló';
                break;
        }
        return $this->state;
    }

    /**
     * Set sender
     *
     * @param \TeamupBundle\Entity\User $sender
     * @return Invitation
     */
    public function setSender(\TeamupBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \TeamupBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set reciever
     *
     * @param \TeamupBundle\Entity\User $reciever
     * @return Invitation
     */
    public function setReciever(\TeamupBundle\Entity\User $reciever = null)
    {
        $this->reciever = $reciever;

        return $this;
    }

    /**
     * Get reciever
     *
     * @return \TeamupBundle\Entity\User 
     */
    public function getReciever()
    {
        return $this->reciever;
    }
}
