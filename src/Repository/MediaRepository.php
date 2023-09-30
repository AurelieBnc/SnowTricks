<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

//    /**
//     * @return Media[] Returns an array of Media objects
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

//    public function findOneBySomeField($value): ?Media
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * function to get only pictures of a post
     * @return array<string>
     */
    public function pictureList(int $idPost) : array
    {
        return $this->createQueryBuilder('m')
            ->select('m.name')
            ->andWhere('m.post = :post')
            ->andWhere('m.category = 1')
            ->setParameter('post', $idPost)
            ->getQuery()
            ->getResult()
       ;
    }

    /**
     * function to get only urls video of a post
     * @return array<string>
     */
    public function videoUrlList(int $idPost) : array
    {
        return $this->createQueryBuilder('m')
            ->select('m.videoUrl')
            ->andWhere('m.post = :post')
            ->andWhere('m.category = 0')
            ->setParameter('post', $idPost)
            ->getQuery()
            ->getResult()
       ;
    }
}
