<?php

namespace TeamupBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="TeamupBundle\Repository\MessageRepository")
 */
class Message
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
     * @ORM\Column(name="msg", type="string", length=255)
     */
    private $msg;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sendedTimestamp", type="datetime")
     */
    private $sendedTimestamp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="readedTimestamp", type="datetime", nullable=true)
     */
    private $readedTimestamp;

    /**
     * Many Messages have One Users.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sendedMessages")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id")
     */
    private $sender;

    /**
     * Many Messages have One Users.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="recievedMessages")
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
     * Set msg
     *
     * @param string $msg
     * @return Message
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;

        return $this;
    }

    /**
     * Get msg
     *
     * @return string 
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * Set sendedTimestamp
     *
     * @param \DateTime $sendedTimestamp
     * @return Message
     */
    public function setSendedTimestamp($sendedTimestamp)
    {
        $this->sendedTimestamp = $sendedTimestamp;

        return $this;
    }

    /**
     * Get sendedTimestamp
     *
     * @return \DateTime 
     */
    public function getSendedTimestamp()
    {
        return $this->sendedTimestamp;
    }

    /**
     * Set readedTimestamp
     *
     * @param \DateTime $readedTimestamp
     * @return Message
     */
    public function setReadedTimestamp($readedTimestamp)
    {
        $this->readedTimestamp = $readedTimestamp;

        return $this;
    }

    /**
     * Get readedTimestamp
     *
     * @return \DateTime 
     */
    public function getReadedTimestamp()
    {
        return $this->readedTimestamp;
    }

    /**
     * Set sender
     *
     * @param \TeamupBundle\Entity\User $sender
     * @return Message
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
     * @return Message
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
