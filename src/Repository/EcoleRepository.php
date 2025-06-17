<?php

namespace App\Repository;

use App\Entity\Ecole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ecole>
 */
class EcoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ecole::class);
    }
    
    /**
     * Recherche des écoles selon différents critères
     */
    public function findBySearchCriteria(?string $searchTerm = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.nom', 'ASC');
        
        if ($searchTerm) {
            $qb->andWhere('e.nom LIKE :searchTerm OR e.code LIKE :searchTerm OR e.ville LIKE :searchTerm')
               ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }
        
        return $qb->getQuery()->getResult();
    }
}
