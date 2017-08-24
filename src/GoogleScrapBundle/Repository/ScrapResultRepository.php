<?php namespace GoogleScrapBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ScrapResultRepository extends EntityRepository
{
    /**
     * @param int $currentPage
     * @return Paginator
     */
    public function getPaginatedScrapResults($currentPage = 1, $paginatorLimit)
    {
        $query = $this->createQueryBuilder('s')
            ->orderBy('s.queryDate', 'ASC')
            ->getQuery();

        $paginator = $this->paginate($query, $currentPage, $paginatorLimit);

        return $paginator;
    }

    /**
     * @param Query $query
     * @param int $page
     * @param int $limit
     * @return Paginator
     */
    private function paginate($query, $page = 1, $paginatorLimit)
    {
        $paginator = new Paginator($query);

        $paginator->getQuery()
            ->setFirstResult($paginatorLimit * ($page - 1))
            ->setMaxResults($paginatorLimit);

        return $paginator;
    }
}