<?php

namespace TeamupBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
	public function wantedUsers()
	{
		$query = $this->getEntityManager()
            ->createQuery(
                'SELECT o FROM TeamupBundle:User u
                JOIN u.team t
                WHERE t.status = 1'
            );
     
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
