<?php

namespace App\Repository;

use App\Entity\Ecole;
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

    /**
     * Récupère les dernières prises de vue pour une école spécifique
     */
    public function findLatestByEcole(Ecole $ecole, int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.ecole', 'e')
            ->leftJoin('p.photographe', 'u')
            ->leftJoin('p.theme', 't')
            ->leftJoin('p.typePrise', 'tp')
            ->leftJoin('p.typeVente', 'tv')
            ->leftJoin('p.planchesIndividuel', 'pi')
            ->leftJoin('p.planchesFratrie', 'pf')
            ->addSelect('e', 'u', 't', 'tp', 'tv', 'pi', 'pf')
            ->where('p.ecole = :ecole')
            ->setParameter('ecole', $ecole)
            ->orderBy('p.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Calcule les statistiques des prises de vue pour une école
     */
    public function getStatsByEcole(Ecole $ecole): array
    {
        // Nombre total de prises de vue
        $totalPrises = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as total')
            ->where('p.ecole = :ecole')
            ->setParameter('ecole', $ecole)
            ->getQuery()
            ->getSingleScalarResult();

        // Nombre total d'élèves photographiés
        $totalEleves = $this->createQueryBuilder('p')
            ->select('SUM(p.nombreEleves) as totalEleves')
            ->where('p.ecole = :ecole')
            ->setParameter('ecole', $ecole)
            ->getQuery()
            ->getSingleScalarResult();

        // Montant total facturé à l'école
        $totalRevenu = $this->createQueryBuilder('p')
            ->select('SUM(p.prixEcole) as totalRevenu')
            ->where('p.ecole = :ecole')
            ->setParameter('ecole', $ecole)
            ->getQuery()
            ->getSingleScalarResult();

        // Nombre total de classes
        $totalClasses = $this->createQueryBuilder('p')
            ->select('SUM(p.nombreClasses) as totalClasses')
            ->where('p.ecole = :ecole')
            ->setParameter('ecole', $ecole)
            ->getQuery()
            ->getSingleScalarResult();

        // Statistiques par année
        // Utilisons une alternative sans fonction d'extration de date
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT YEAR(p.date) as year, 
                   COUNT(p.id) as totalPrises, 
                   SUM(p.nombre_eleves) as totalEleves, 
                   SUM(p.prix_ecole) as totalRevenu
            FROM prise_de_vue p
            WHERE p.ecole_id = :ecoleId
            GROUP BY YEAR(p.date)
            ORDER BY year DESC
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['ecoleId' => $ecole->getId()]);
        $statsByYear = $resultSet->fetchAllAssociative();

        // Types de prises les plus fréquents
        $typePrisesStats = $this->createQueryBuilder('p')
            ->select('tp.name, COUNT(p.id) as count')
            ->leftJoin('p.typePrise', 'tp')
            ->where('p.ecole = :ecole')
            ->setParameter('ecole', $ecole)
            ->groupBy('tp.name')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();

        // Thèmes les plus fréquents
        $themesStats = $this->createQueryBuilder('p')
            ->select('t.name, COUNT(p.id) as count')
            ->leftJoin('p.theme', 't')
            ->where('p.ecole = :ecole')
            ->setParameter('ecole', $ecole)
            ->groupBy('t.name')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();

        return [
            'totalPrises' => $totalPrises ?: 0,
            'totalEleves' => $totalEleves ?: 0,
            'totalRevenu' => $totalRevenu ?: 0,
            'totalClasses' => $totalClasses ?: 0,
            'statsByYear' => $statsByYear,
            'typePrisesStats' => $typePrisesStats,
            'themesStats' => $themesStats
        ];
    }
}