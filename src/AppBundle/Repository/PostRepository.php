<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{
    public function getPostsWithOffset($lastItem, $limit)
    {
        $qb = $this->createQueryBuilder('post');

        $qb->addOrderBy('post.id', 'DESC')
          ->where($qb->expr()->gt('post.id', ':lastitem'))
          ->setParameter(':lastitem', $lastItem)
          ->setMaxResults($limit);

        $query = $qb->getQuery();
        $query->useResultCache(true, 3600);

        return $query->getResult();
    }

    public function count()
    {
        return $this->createQueryBuilder('post')
          ->select('count(post.id)')
          ->getQuery()
          ->getSingleScalarResult();
    }
    
}