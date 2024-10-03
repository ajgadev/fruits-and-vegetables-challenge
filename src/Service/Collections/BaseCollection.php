<?php

namespace App\Service\Collections;

use App\Enum\WeightUnit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


abstract class BaseCollection implements CollectionInterface
{
    protected ServiceEntityRepository $repository;
    protected EntityManagerInterface $entityManager;

    public function __construct(
        ServiceEntityRepository $repository,
        EntityManagerInterface $entityManager
    )
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function add($item): void
    {
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    public function remove($item): void
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    public function list(?string $name = null, int $page = 1, int $limit = 10, string $unit = WeightUnit::GRAM): array
    {
        $queryBuilder = $this->repository->createQueryBuilder('f');

        if ($name) {
            $queryBuilder->andWhere('f.name LIKE :name')
                         ->setParameter('name', '%' . $name . '%');
        }
        // Separate query for counting total items (without pagination)
        $countQueryBuilder = clone $queryBuilder;
        $totalItems = $countQueryBuilder->select('COUNT(f.id)')
                                        ->getQuery()
                                        ->getSingleScalarResult();

        $query = $queryBuilder->setFirstResult(($page - 1) * $limit)
                              ->setMaxResults($limit)
                              ->getQuery();
        $results = $query->getResult();
        
        foreach ($results as $item) {
            if ($unit !== WeightUnit::GRAM) {
                $quantityInGrams = $item->getQuantity();
                $convertedQuantity = WeightUnit::convert($quantityInGrams, WeightUnit::GRAM, $unit);
                $item->setQuantity($convertedQuantity);
            }
            $item->setWeightUnit($unit);
        }
        return [$results, $totalItems];
    }


    // Didn't test it yet, but it should work
    public function search(array $criteria, int $page = 1, int $limit = 10, string $unit = WeightUnit::GRAM): array
    {
        $queryBuilder = $this->repository->createQueryBuilder('f');

        foreach ($criteria as $key => $value) {
            if ($key === 'name') {
                $queryBuilder->andWhere('f.name LIKE :name')
                             ->setParameter('name', '%' . $value . '%');
            } else {
                $queryBuilder->andWhere("f.$key = :$key")
                             ->setParameter($key, $value);
            }
        }
        // Separate query for counting total items (without pagination)
        $countQueryBuilder = clone $queryBuilder;
        $totalItems = $countQueryBuilder->select('COUNT(f.id)')
                                        ->getQuery()
                                        ->getSingleScalarResult();

        $query = $queryBuilder->setFirstResult(($page - 1) * $limit)
                              ->setMaxResults($limit)
                              ->getQuery();
        $results = $query->getResult();

        foreach ($results as $item) {
            $quantityInGrams = $item->getQuantity();
            $convertedQuantity = WeightUnit::convert($quantityInGrams, WeightUnit::GRAM, $unit);
            $item->setQuantity($convertedQuantity);
            $item->setWeightUnit($unit);
        }

        return [$results, $totalItems];
    }

    public function findById(int $id): ?object
    {
        return $this->repository->find($id);
    }

    public function getRepository(): ObjectRepository
    {
        return $this->repository;
    }
}