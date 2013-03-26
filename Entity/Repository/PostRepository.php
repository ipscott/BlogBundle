<?php

namespace Desarrolla2\Bundle\BlogBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;
use \Desarrolla2\Bundle\BlogBundle\Entity\Post;
use \Desarrolla2\Bundle\BlogBundle\Entity\Tag;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends EntityRepository {

    const POST_PER_PAGE = 6;

    public function getByIds(array $ids) {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            ' SELECT p FROM BlogBundle:Post p ' .
            ' WHERE p.id IN (:ids) ' .
            ' AND p.isPublished = 1 '
        )
        ->setParameter('ids', $ids);
                
                
        return $query->getResult();
    }

    /**
     * 
     * @param string $slug
     * @return \Desarrolla2\Bundle\BlogBundle\Entity\Post
     */
    public function getOneBySlug($slug) {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                        ' SELECT p FROM BlogBundle:Post p ' .
                        ' WHERE p.slug = :slug ' .
                        ' ORDER BY p.publishedAt DESC '
                )
                ->setParameter('slug', $slug)
        ;
        return $query->getOneOrNullResult();
    }

    /**
     * 
     * @param string $slug
     * @return \Doctrine\ORM\Query
     */
    public function getByTag(Tag $tag, $limit = self::POST_PER_PAGE) {
        $limit = (int) $limit;
        $query = $this->getQueryForGetByTag($tag, $limit)
                ->setMaxResults($limit)
        ;
        return $query->getResult();
    }

    /**
     * 
     * @param type $limit
     * @return array
     */
    public function get($limit = self::POST_PER_PAGE) {
        $limit = (int) $limit;
        $query = $this->getQueryForGet($limit)
                ->setMaxResults($limit)
        ;
        return $query->getResult();
    }

    /**
     * 
     * @return \Doctrine\ORM\Query
     */
    public function getQueryForGet() {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                ' SELECT p FROM BlogBundle:Post p ' .
                ' WHERE p.isPublished = 1 ' .
                ' ORDER BY p.publishedAt DESC '
                )
        ;
        return $query;
    }

    /**
     * 
     * @param type $slug
     * @return array
     */
    public function getByTagSlug($slug = '') {
        $query = $this->getQueryForGetByTagSlug($slug);
        return $query->getResult();
    }

    /**
     * 
     * @param string $slug
     * @return \Doctrine\ORM\Query
     */
    public function getQueryForGetByTag(Tag $tag) {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                        ' SELECT p FROM BlogBundle:Post p ' .
                        ' JOIN p.tags t ' .
                        ' WHERE p.isPublished = 1 ' .
                        ' AND t.slug  = :slug ' .
                        ' ORDER BY p.publishedAt DESC '
                )
                ->setParameter('slug', $tag->getSlug())
        ;
        return $query;
    }

    /**
     * 
     * @param string $slug
     * @return \Doctrine\ORM\Query
     */
    public function getQueryForGetByTagSlug($slug = '') {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                        ' SELECT p FROM BlogBundle:Post p ' .
                        ' JOIN p.tags t ' .
                        ' WHERE p.isPublished = 1 ' .
                        ' AND t.slug = :slug ' .
                        ' ORDER BY p.publishedAt DESC '
                )
                ->setParameter('slug', $slug)
        ;
        return $query;
    }

    /**
     * 
     * @param int $limit
     * @return array
     */
    public function getLatestRelated(Post $post, $limit = self::POST_PER_PAGE) {
        $limit = (int) $limit;
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                        ' SELECT p FROM BlogBundle:Post p ' .
                        ' JOIN p.tags t ' .
                        ' JOIN t.posts p1 ' .
                        ' WHERE p.isPublished = 1 ' .
                        ' AND p1 = :post ' .
                        ' ORDER BY p.publishedAt DESC '
                )
                ->setParameter('post', $post)
                ->setMaxResults($limit)
        ;
        $related = $query->getResult();
        if (count($related)) {
            return $related;
        } else {
            return $this->getLatest($limit);
        }
    }

    /**
     * 
     * @param int $limit
     * @return array
     */
    public function getLatest($limit = self::POST_PER_PAGE) {
        $limit = (int) $limit;
        return $this->get($limit);
    }

    /**
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForFilter() {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb
                ->select('p')
                ->from('BlogBundle:Post', 'p')
                ->orderBy('p.updatedAt', 'DESC')
        ;
        return $qb;
    }

    /**
     * 
     * @return int
     */
    public function count() {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                ' SELECT COUNT(p) FROM BlogBundle:Post p '
                )
        ;
        return $query->getSingleScalarResult();
    }

    /**
     * 
     * @return int
     */
    public function countPublished() {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                ' SELECT COUNT(p) FROM BlogBundle:Post p ' .
                ' WHERE p.isPublished = 1 '
                )
        ;
        return $query->getSingleScalarResult();
    }

    /**
     * 
     * @return type
     */
    public function getUnPublished() {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                ' SELECT p FROM BlogBundle:Post p ' .
                ' WHERE p.isPublished = 0 ' .
                ' ORDER BY p.createdAt DESC '
                )
        ;
        return $query->getResult();
    }

}

