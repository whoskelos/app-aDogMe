<?php

namespace App\Repository;

use App\Entity\Perro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<Perro>
 *
 * @method Perro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Perro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Perro[]    findAll()
 * @method Perro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PerroRepository extends ServiceEntityRepository
{
    private $usuario;
    public function __construct(Security $security, ManagerRegistry $registry)
    {
        parent::__construct($registry, Perro::class);
        $this->usuario = $security->getUser();
    }

    public function paginacion($dql, $pagina, $elementoPorPagina)
    {
        $paginador = new Paginator($dql);
        $paginador->getQuery()
        ->setFirstResult($elementoPorPagina * ($pagina -1))
        ->setMaxResults($elementoPorPagina);
        return $paginador;
    }

    public function buscarTodas($pagina = 1, $elementoPorPagina = 5)
    {
        $query = $this->createQueryBuilder('p')
            ->addOrderBy('p.peso', 'DESC')
            ->andWhere('p.usuario = :usuario')
            ->setParameter('usuario', $this->usuario)
           ->getQuery()
       ;

       return $this->paginacion($query, $pagina, $elementoPorPagina);
    }

    public function listarTodas($pagina = 1, $elementoPorPagina = 5)
    {
        $query = $this->createQueryBuilder('p')
           ->getQuery()
       ;

       return $this->paginacion($query, $pagina, $elementoPorPagina);
    }

    public function add(Perro $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Perro $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Perro[] Returns an array of Perro objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Perro
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
