<?php

namespace App\Repository;

use App\Entity\Classe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Classe>
 */
class ClasseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classe::class);
    }
    public function findOneRandomWithBords()
    {
        // Récupérer les sections avec au moins 4 bords
        $sections = $this->createQueryBuilder('s')
            ->innerJoin('s.bords', 'b')
            ->where('b.is_published = :published')
            ->setParameter('published', true)
            ->groupBy('s.id')
            ->having('COUNT(b.id) >= 3')
            ->getQuery()
            ->getResult();

        if (count($sections) == 0) {
            return null;
        }

        // Sélectionner une section aléatoire
        $randomIndex = array_rand($sections);

        return $sections[$randomIndex];
    }
    //    /**
    //     * @return Classe[] Returns an array of Classe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Classe
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
