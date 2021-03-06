<?php

namespace TeamupBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends EntityRepository
{
	public function chatMessages($user, $currentUser)
	{
		$query = $this->getEntityManager()
            ->createQuery(
                'SELECT m FROM TeamupBundle:Message m
                JOIN m.reciever r
                JOIN m.sender s
                WHERE ( r.id = :currentUserId AND s.id = :userId ) OR ( r.id = :userId AND s.id = :currentUserId )
                ORDER BY m.sendedTimestamp
                '
            )->setParameters(array('currentUserId' => $currentUser->getId(), 'userId' => $user->getId()));
     
        try 
        {
            return $query->getResult();
        } 
        catch (\Doctrine\ORM\NoResultException $e) 
        {
            return null;
        }
	}

    public function usersWithMessages($user)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT u FROM TeamupBundle:User u
                JOIN u.sendedMessages sm
                JOIN u.recievedMessages rm
                JOIN sm.reciever ur
                JOIN rm.sender us
                WHERE ur.id = :userId OR us.id = :userId
                ORDER BY u.name
                '
            )->setParameters(array('userId' => $user->getId() ));
     
        try 
        {
            return $query->getResult();
        } 
        catch (\Doctrine\ORM\NoResultException $e) 
        {
            return null;
        }
    }

    public function findLatest($message, $user, $currentUser)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT m FROM TeamupBundle:Message m
                JOIN m.reciever r
                JOIN m.sender s
                WHERE ( ( r.id = :currentUserId AND s.id = :userId ) OR ( r.id = :userId AND s.id = :currentUserId ) ) AND m.id > :lastId
                ORDER BY m.sendedTimestamp
                '
            )->setParameters(array( 'currentUserId' => $currentUser->getId(), 'userId' => $user->getId(), 'lastId' => $message->getId() ));
     
        try 
        {
            return $query->getResult();
        } 
        catch (\Doctrine\ORM\NoResultException $e) 
        {
            return null;
        }
    }
}
