<?php

namespace TeamupBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PetitionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PetitionRepository extends EntityRepository
{
	public function findOthersOfSameRecieverTeam($petition)
	{
        $senderId = $petition->getSender()->getId();
        $recieverId = $petition->getReciever()->getId();

		$query = $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM TeamupBundle:Petition p
                JOIN p.reciever r
                JOIN p.sender s
                JOIN r.team t
                JOIN t.user u
                WHERE u.id = r.id AND s.id = :senderId AND r.id = :recieverId
                '
            )->setParameters(array('senderId' => $senderId, 'recieverId' => $recieverId) );
     
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
