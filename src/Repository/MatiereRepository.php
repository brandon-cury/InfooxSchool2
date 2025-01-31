<?php

namespace App\Repository;

use App\Entity\Matiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Matiere>
 */
class MatiereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matiere::class);
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
    public function findPublishedBords(Matiere $matiere)
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.bords', 'b')
            ->where('m.id = :matiere_id')
            ->andWhere('b.isPublished = true')
            ->setParameter('matiere_id', $matiere->getId())
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Matiere[] Returns an array of Matiere objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Matiere
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
