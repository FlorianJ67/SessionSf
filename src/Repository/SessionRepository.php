<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function save(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    // Afficher les stagiaires non inscrits
    public function findNonInscrits($session_id) {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner tous les stagiaires d'une session dont l'id est passé en paramètre
        $qb->select('s')
            ->from('App\Entity\Stagiaire', 's')
            ->leftJoin('s.sessions', 'se')
            ->where('se.id = :id');

        $sub = $em->createQueryBuilder();
        // sélectionner tous les stagiaires qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les stagiaires non inscrits pour une session définie
        $sub->select('st')
            ->from('App\Entity\Stagiaire','st')
            ->where($sub->expr()->notIn('st.id', $qb->getDQL()))
            // requête parametrée
            ->setParameter('id', $session_id)
            // trier la liste des stagiaire sur le nom de famille
            ->orderBy('st.lastName');

        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();
    }
    // !!!  le bug est là  !!!
    // !!! !!! !!! !!! !!! !!!
    public function findNonModule($session_id) {
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qb = $sub;
        // sélectionner tous les modules d'une session dont l'id est passé en paramètre
        $qb->select('m')
            ->from('App\Entity\Module', 'm')
            ->leftJoin('m.sessions', 'se')
            ->where('se.id = :id');

        $sub = $em->createQueryBuilder();
        // sélectionner tous les modules qui ne SONT PAS (NOT IN) dans le résultat précédent
        // on obtient donc les modules non inscrits pour une session définie
        $sub->select('mo')
            ->from('App\Entity\Module','mo')
            ->where($sub->expr()->notIn('mo.id', $qb->getDQL()))
            // requête parametrée
            ->setParameter('id', $session_id)
            // trier la liste des module sur le nom
            ->orderBy('mo.name');

        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();
    }

//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
