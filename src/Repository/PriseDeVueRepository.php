<?php

namespace App\Repository;

use App\Entity\PriseDeVue;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriseDeVue>
 */
class PriseDeVueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriseDeVue::class);
    }

    /**
     * Recherche des prises de vue selon différents critères
     */
    public function findBySearchCriteria(array $criteria = [], ?User $photographer = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.ecole', 'e')
            ->leftJoin('p.photographe', 'u')
            ->leftJoin('p.theme', 't')
            ->leftJoin('p.typePrise', 'tp')
            ->leftJoin('p.typeVente', 'tv')
            ->addSelect('e', 'u', 't', 'tp', 'tv') // Optimisation N+1
            ->orderBy('p.date', 'DESC'); // Plus récent en premier
        
        // Filtrer par photographe spécifique (si le photographe est connecté)
        if ($photographer !== null) {
            $qb->andWhere('p.photographe = :photographer')
               ->setParameter('photographer', $photographer);
        }

        // Recherche par terme dans l'école ou le commentaire
        if (!empty($criteria['searchTerm'])) {
            $qb->andWhere('e.nom LIKE :searchTerm OR p.commentaire LIKE :searchTerm')
               ->setParameter('searchTerm', '%' . $criteria['searchTerm'] . '%');
        }

        // Filtrer par période (date début et fin)
        if (!empty($criteria['dateDebut'])) {
            $qb->andWhere('p.date >= :dateDebut')
               ->setParameter('dateDebut', $criteria['dateDebut']->setTime(0, 0, 0));
        }

        if (!empty($criteria['dateFin'])) {
            $qb->andWhere('p.date <= :dateFin')
               ->setParameter('dateFin', $criteria['dateFin']->setTime(23, 59, 59));
        }

        // Filtrer par photographe (si sélectionné dans le formulaire)
        if (!empty($criteria['photographe'])) {
            $qb->andWhere('p.photographe = :photographe')
               ->setParameter('photographe', $criteria['photographe']);
        }

        // Filtrer par thème
        if (!empty($criteria['theme'])) {
            $qb->andWhere('p.theme = :theme')
               ->setParameter('theme', $criteria['theme']);
        }

        // Filtrer par type de prise
        if (!empty($criteria['typePrise'])) {
            $qb->andWhere('p.typePrise = :typePrise')
               ->setParameter('typePrise', $criteria['typePrise']);
        }

        // Filtrer par type de vente
        if (!empty($criteria['typeVente'])) {
            $qb->andWhere('p.typeVente = :typeVente')
               ->setParameter('typeVente', $criteria['typeVente']);
        }

        // Filtrer par école
        if (!empty($criteria['ecole'])) {
            $qb->andWhere('p.ecole = :ecole')
               ->setParameter('ecole', $criteria['ecole']);
        }

        return $qb->getQuery()->getResult();
    }
}