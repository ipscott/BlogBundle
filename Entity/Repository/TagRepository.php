<?php

namespace Desarrolla2\Bundle\BlogBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Desarrolla2\Bundle\BlogBundle\Entity\Tag;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository {

    const TAGS_PER_PAGE = 20;

    /**
     * 
     * @param type $limit
     * @return array
     */
    public function get($limit = self::TAGS_PER_PAGE) {
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
                ' SELECT t FROM BlogBundle:Tag t ' .
                ' WHERE t.items >= 1 ' .
                ' ORDER BY t.items DESC '
                )
        ;
        return $query;
    }

    /**
     * 
     * @param type $limit
     * @return type
     */
    public function getQueryBuilderForGet($limit = self::TAGS_PER_PAGE) {
        $limit = (int) $limit;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
                ->select('t')
                ->from('BlogBundle:Tag', 't')
                ->orderBy('t.items', 'DESC')
                ->setMaxResults($limit);
        return $qb;
    }

    /**
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForFilter() {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
                ->select('t')
                ->from('BlogBundle:Tag', 't')
                ->orderBy('t.items', 'DESC')
        ;
        return $qb;
    }

    /**
     * 
     * @param \Desarrolla2\Bundle\BlogBundle\Entity\Tag $t
     * @return \Doctrine\ORM\Query
     */
    public function getQueryForCountItemsForTag(Tag $t) {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                        ' SELECT COUNT(p) FROM BlogBundle:Post p ' .
                        ' JOIN p.tags t ' .
                        ' WHERE p.isPublished = 1 ' .
                        ' AND t = :t ' .
                        ' ORDER BY p.createdAt DESC '
                )
                ->setParameter('t', $t);

        return $query;
    }

    /**
     * 
     * @param \Desarrolla2\Bundle\BlogBundle\Entity\Tag $tag
     * @return integer
     */
    public function indexTagItemsForTag(Tag $tag) {
        $em = $this->getEntityManager();
        $n = $this->getQueryForCountItemsForTag($tag)
                ->getSingleScalarResult();
        $tag->setItems($n);
        $em->persist($tag);
        $em->flush();
    }

    /**
     * Set total items for all tags
     */
    public function indexTagItems() {
        foreach ($this->findAll() as $tag) {
            $this->indexTagItemsForTag($tag);
        }
    }

    public function count() {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                ' SELECT COUNT(t) FROM BlogBundle:Tag t '
                )
        ;
        return $query->getSingleScalarResult();
    }

    /**
     * 
     * @param string $slug
     * @return \Desarrolla2\Bundle\BlogBundle\Entity\Tag
     */
    public function getOneBySlug($slug) {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                        ' SELECT t FROM BlogBundle:Tag t ' .
                        ' WHERE t.slug = :slug' .
                        ' ORDER BY t.createdAt DESC '
                )
                ->setParameter('slug', $slug)
        ;
        return $query->getOneOrNullResult();
    }

    /**
     * 
     * @param string $tagName
     * @return \Desarrolla2\Bundle\BlogBundle\Entity\Tag
     */
    public function getOrCreateByName($tagName) {
        $name = strtolower($tagName);
        $em = $this->getEntityManager();
        $query = $em->createQuery(
                        ' SELECT t FROM BlogBundle:Tag t ' .
                        ' WHERE t.name = :name'
                )
                ->setParameter('name', $name)
        ;
        $tag = $query->getOneOrNullResult();
        if (!$tag) {
            $tag = new Tag();
            $tag->setName($name);
            $em->persist($tag);
            $em->flush();
        }
        return $tag;
    }

}
