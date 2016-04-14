<?php
/**
 * Created by PhpStorm.
 * User: gdelre
 * Date: 13/04/16
 * Time: 20:29
 */

namespace CoreBundle\Entity\Repository;


class CategoryRepository extends AbstractRepository
{
    /**
     * @param array $categories
     * @return int
     */
    public function sync(array $categories)
    {
        $added = 0;

        foreach($categories as $key => $category) {
            $found = $this->findOneByName($category);

            if (null === $found) {
                $entity = new $this->getClassMetadata();
                $entity->setName($category);
                $this->save($entity);
                $added++;
            }
        }

        return $added;
    }

    /**
     * @param $ids
     * @return array
     */
    public function findWhereIdIn($ids)
    {
        $categories = $this->createQueryBuilder('c')
            ->where('c.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        $result = [];

        foreach($categories as $category) {
            $result[] = $category->getName();
        }

        return $result;
    }
}