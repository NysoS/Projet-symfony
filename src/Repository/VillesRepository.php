<?php

namespace App\Repository;

use App\Entity\Villes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Villes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Villes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Villes[]    findAll()
 * @method Villes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VillesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Villes::class);
    }

    /**
     * @return Villes[] Returns an array of Villes objects
     */
    public function findByFieldName($value)
    {
        //je recherche toutes les valeurs qui correspondent au mot clé grâce à l'attribut 
        // where et like %
        //SELECT * FROM ville WHERE nom_ville or codePostal LIKE %mot clé% =>en DQL 
        return $this->createQueryBuilder('v')
            ->Where('v.nom_ville LIKE :val')
            ->orWhere('v.code_postal LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->orderBy('v.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Villes[] Returns an array of Villes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Villes
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
