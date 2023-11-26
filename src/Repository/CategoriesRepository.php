<?php

namespace App\Repository;

use App\Entity\Categories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categories>
 *
 * @method Categories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categories[]    findAll()
 * @method Categories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriesRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct
    (
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    ) {
        parent::__construct($registry, Categories::class);
        $this->manager = $manager;
    }

    public function saveCategories($name, $description, $deleted, $createdAt)
    {
        $newCategories = new Categories();

        $newCategories
            ->setName($name)
            ->setDescription($description)
            ->setDeleted($deleted)
            ->setCreatedAt($createdAt);

        $this->manager->persist($newCategories);
        $this->manager->flush();
        return $newCategories;
    }

    public function updateCategories(Categories $categories): Categories
    {
        $this->manager->persist($categories);
        $this->manager->flush();
        return $categories;
    }

    public function removeCategories(Categories $categories)
    {
        $categories->setDeleted(true);
        $this->manager->persist($categories);
        $this->manager->flush();
    }

}
