<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method Sorties|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sorties|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sorties[]    findAll()
 * @method Sorties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }

    public function filtersHomeSorties(Request $req, Participant $p = null)
    {
        $qb = $this->createQueryBuilder('s');

        if ($req->get('filter_site') == 'default') {
        } else if ($req->get('filter_site') != null) {
            $qb->where('s.site = :idSite')
                ->setParameter(':idSite', $req->get('filter_site'));
        }

        if($req->get('sortie_contains') != null){
            $qb->where('s.nom like :ns')
                ->setParameter(':ns','%'.$req->get('sortie_contains').'%');
        }

        if($req->get('filter_d1') != null and $req->get('filter_d2') != null){
            $qb->where('s.date_debut >= :d1 and s.date_cloture <= :d2')
                ->setParameter(':d1',$req->get('filter_d1'))
                ->setParameter(':d2',$req->get('filter_d2'));
        }
       
        if($req->get('ch_s_1') != null){
            $qb->where('s.organisateur = :idP')
                ->setParameter(':idP', $p->getId());
        }


        if($req->get('ch_s_2') != null and $req->get('ch_s_3') != null){
        }else{
            if($req->get('ch_s_2') != null){
                $qb->innerJoin('s.inscriptions', 'i')
                    ->addSelect('i')
                    ->where('i.id = s.inscriptions')
                    ->where('i.participants = :idP2')
                    ->setParameter(':idP2', $p->getId());
            }
    
            if($req->get('ch_s_3') != null){
                $qb->innerJoin('s.inscriptions', 'i')
                    ->addSelect('i')
                    ->where('i.id = s.inscriptions')
                    ->where('i.participants <> :idP3')
                    ->setParameter(':idP3', $p->getId());
            }
        }

        if($req->get('ch_s_4') != null){
            $qb->innerJoin('s.etats', 'e')
                ->addSelect('e')
                ->where('e.id = s.etats')
                ->where('e.libelle = \'passée\'');
        }

        $query = $qb->getQuery();
        return $query->execute();
    }

    // /**
    //  * @return Sorties[] Returns an array of Sorties objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sorties
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
