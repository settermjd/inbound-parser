<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Note;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class NoteRepositoryFactory
{
    public function __invoke(ContainerInterface $container): NoteRepository
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);

        return $entityManager->getRepository(Note::class);
    }
}
