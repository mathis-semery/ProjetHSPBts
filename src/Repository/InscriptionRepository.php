<?php

namespace App\Repository;

use App\Entity\Evenement;
use App\Entity\Inscription;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inscription>
 */
class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    /**
     * Vérifie si un utilisateur est déjà inscrit à un événement
     */
    public function findByUserAndEvenement(User $user, Evenement $evenement): ?Inscription
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.refUser = :user')
            ->andWhere('i.refEvenement = :evenement')
            ->setParameter('user', $user)
            ->setParameter('evenement', $evenement)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère tous les inscrits confirmés d'un événement (etat = true)
     * @return Inscription[]
     */
    public function findConfirmedByEvenement(Evenement $evenement): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.refEvenement = :evenement')
            ->andWhere('i.etat = true')
            ->setParameter('evenement', $evenement)
            ->orderBy('i.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les inscrits confirmés d'un événement
     */
    public function countConfirmedByEvenement(Evenement $evenement): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->andWhere('i.refEvenement = :evenement')
            ->andWhere('i.etat = true')
            ->setParameter('evenement', $evenement)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
