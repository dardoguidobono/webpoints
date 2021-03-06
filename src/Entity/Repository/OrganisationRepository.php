<?php
namespace Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class OrganisationRepository
 * @package Entity\Repository
 */
class OrganisationRepository extends EntityRepository {

    /**
     * @param int $page
     * @return array
     */
    public function getAllSorted( $page = 1 ){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from('Entity\Organisation', 'u')
            ->orderBy('u.name');
        return $this->paginate( $qb->getQuery(), $page, 10, false );
    }

    /**
     * @param $name
     * @param int $page
     * @return array
     */
    public function search( $name , $page = 1){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from('Entity\Organisation', 'u')
            ->where('u.name LIKE :name')
            ->setParameter('name', "%". $name . "%")
            ->orderBy('u.name');
        return $this->paginate( $qb->getQuery(), $page, 10, false );
    }


    /**
     * @param Query $query
     * @param $page
     * @param $limit
     * @param bool $fetchJoinCollection
     * @return array
     */
    function paginate(Query $query, $page, $limit, $fetchJoinCollection = true)
    {
        // If we don't have a page just return the result
        if( ! $page && ! $limit)
        {
            return $query->getResult();
        }
        $offset = ($page * $limit) - $limit;
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);
        $paginator = new Paginator($query, $fetchJoinCollection);


        $count = $paginator->count();
        $pagination_info = [
            'total' => (int) $count,
            'current_page' => (int) $page,
            'total_pages' => (int) ceil($count / $limit)
        ];

        return [
            'result' => $paginator,
            'meta' => $pagination_info
        ];
    }
}