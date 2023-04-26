<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 *
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    public function save(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Afficher les session non inscrits dans la formation
    public function findNonInscrits($formation_id) {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner tous les sessions d'une formation dont l'id est passé en paramètre
        $qb->select('s')
            ->from('App\Entity\Session', 's')
            ->leftJoin('s.formation', 'f')
            ->where('f.id = :id');

        $sub = $em->createQueryBuilder();
        // sélectionner tous les sessions qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les sessions non inscrits pour une formation définie
        $sub->select('se')
            ->from('App\Entity\Session','se')
            ->where($sub->expr()->notIn('se.id', $qb->getDQL()))
            // requête parametrée
            ->setParameter('id', $formation_id)
            // trier la liste des stagiaire sur le nom de famille
            ->orderBy('se.name');

        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();
    }

//    /**
//     * @return Formation[] Returns an array of Formation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Formation
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
