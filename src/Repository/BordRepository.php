<?php

namespace App\Repository;

use App\Entity\Bord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Bord>
 */
class BordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Bord::class);
    }
    public function findByFilters(array $filtres = [], array $sort = ['all_user'=> 'DESC'], int $limit = 15)
    {
        //filtrage
        $queryBuilder = $this->createQueryBuilder('b');
        foreach ($filtres as $champ => $valeur) {
            if ($valeur !== null) {
                if(!in_array($champ, ['section', 'classe', 'filiere', 'matiere', 'collection'])){
                    $queryBuilder->andWhere("b.$champ = :$champ")
                        ->setParameter($champ, $valeur);
                }else{
                    if(in_array($champ, ['collection'])){
                        $queryBuilder->join("b.$champ", 't')
                            ->andWhere('t.id = :manyToOneId')
                            ->setParameter('manyToOneId', $valeur);
                    }else{
                        $queryBuilder->join("b.$champ", 't')
                            ->andWhere('t.id = :manyToManyId')
                            ->setParameter('manyToManyId', $valeur);
                    }

                }
            }
        }
        //livre publier
        $queryBuilder->andWhere('b.is_published = :is_published')
            ->setParameter('is_published', true);


        //trie
        foreach ($sort as $field => $direction) {
            $queryBuilder->addOrderBy("b.$field", $direction);
        }
        if ($limit) {
            $queryBuilder->setMaxResults($limit);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findBordsByCriteria(string $criteria, string $page, int $limit = 15): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('b');

        // Extraire les IDs de la chaîne de critères
        preg_match('/m(\d*)/', $criteria, $matiereMatch);
        preg_match('/c(\d*)/', $criteria, $classeMatch);
        preg_match('/f(\d*)/', $criteria, $filiereMatch);

        if (!empty($matiereMatch[1])) {
            $queryBuilder->join('b.matiere', 'm')
                ->andWhere('m.id = :matiereId')
                ->setParameter('matiereId', $matiereMatch[1]);
        }

        if (!empty($classeMatch[1])) {
            $queryBuilder->join('b.classe', 'c')
                ->andWhere('c.id = :classeId')
                ->setParameter('classeId', $classeMatch[1]);
        }

        if (!empty($filiereMatch[1])) {
            $queryBuilder->join('b.filiere', 'f')
                ->andWhere('f.id = :filiereId')
                ->setParameter('filiereId', $filiereMatch[1]);
        }

        $queryBuilder->andWhere('b.is_published = :published')
            ->setParameter('published', true)
            ->orderBy('b.all_user', 'DESC');


        $query = $queryBuilder->getQuery()->getResult();
        return $this->paginator->paginate($query, $page, $limit);


    }

    public function findBordsByExamen(string $criteria, string $page, int $limit = 15): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('b');

        // Extraire les IDs de la chaîne de critères
        preg_match('/e(\d*)/', $criteria, $matiereMatch);

        if (!empty($matiereMatch[1])) {
            $examenId = $matiereMatch[1];

            // Jointure avec les filières et examens
            $queryBuilder->join('b.classe', 'c')
                ->join('c.examen', 'e')
                ->andWhere('e.id = :examenId')
                ->setParameter('examenId', $examenId);
        }

        $queryBuilder->andWhere('b.is_published = :published')
            ->setParameter('published', true)
            ->orderBy('b.all_user', 'DESC');

        $query = $queryBuilder->getQuery();

        return $this->paginator->paginate($query, $page, $limit);
    }
    public function findCoursOrderedBySort(int $bordId)
    {
        return $this->createQueryBuilder('b')
            ->join('b.cours', 'c')
            ->addSelect('c')
            ->where('b.id = :bordId')
            ->setParameter('bordId', $bordId)
            ->orderBy('c.sort', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchBords(?string $search, int $page, int $limit = 15) : PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('b');

        if (!is_null($search) && $search !== '') {
            // Retirer les espaces au début et à la fin de la chaîne de recherche
            $search = trim($search);

            // Ajouter la condition de recherche pour la chaîne intégrale
            $queryBuilder->andWhere('b.title LIKE :fullSearch OR b.author LIKE :fullSearch OR b.keyword LIKE :fullSearch OR b.small_description LIKE :fullSearch OR b.full_description LIKE :fullSearch')
                ->setParameter('fullSearch', '%' . $search . '%');

            // Diviser la chaîne en mots individuels
            $searchTerms = explode(' ', $search);

            // Construire les conditions de recherche pour chaque combinaison de deux mots
            for ($i = 0; $i < count($searchTerms); $i++) {
                for ($j = $i + 1; $j < count($searchTerms); $j++) {
                    $term1 = $searchTerms[$i];
                    $term2 = $searchTerms[$j];
                    $queryBuilder->orWhere('b.title LIKE :term' . $i . '_' . $j . ' OR b.author LIKE :term' . $i . '_' . $j . ' OR b.keyword LIKE :term' . $i . '_' . $j . ' OR b.small_description LIKE :term' . $i . '_' . $j . ' OR b.full_description LIKE :term' . $i . '_' . $j)
                        ->setParameter('term' . $i . '_' . $j, '%' . $term1 . ' ' . $term2 . '%');
                }
            }
        }

        $queryBuilder->andWhere('b.is_published = :published')
            ->setParameter('published', true)
            ->orderBy('b.all_user', 'DESC');

        $query = $queryBuilder->getQuery();
        return $this->paginator->paginate($query, $page, $limit);
    }




//    /**
//     * @return Bord[] Returns an array of Bord objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bord
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
