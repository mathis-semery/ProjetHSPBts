<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour transformer les métiers en rôles
 * - Tous les utilisateurs auront ROLE_USER par défaut (géré automatiquement par getRoles())
 * - Les métiers deviennent des rôles : ROLE_ELEVE, ROLE_MEDECIN, ROLE_PARTENAIRE
 * - Le champ métier reste à titre indicatif
 */
final class Version20251128UpdateUserRoles extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Transforme les métiers en rôles pour simplifier le système de permissions';
    }

    public function up(Schema $schema): void
    {
        // Récupérer tous les utilisateurs
        $users = $this->connection->fetchAllAssociative('SELECT id, roles, metier FROM user');

        foreach ($users as $user) {
            $userId = $user['id'];
            $currentRoles = json_decode($user['roles'], true) ?? [];
            $metier = strtoupper(trim($user['metier'] ?? ''));

            // Mapping des métiers vers les rôles
            $metierToRole = [
                'ELEVE' => 'ROLE_ELEVE',
                'ETUDIANT' => 'ROLE_ELEVE',
                'MEDECIN' => 'ROLE_MEDECIN',
                'PARTENAIRE' => 'ROLE_PARTENAIRE',
            ];

            // Si le métier correspond à un rôle connu, l'ajouter
            if (isset($metierToRole[$metier]) && !in_array($metierToRole[$metier], $currentRoles)) {
                $currentRoles[] = $metierToRole[$metier];
            }

            // Mettre à jour les rôles
            $newRoles = json_encode(array_values(array_unique($currentRoles)));
            $this->addSql('UPDATE user SET roles = :roles WHERE id = :id', [
                'roles' => $newRoles,
                'id' => $userId,
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        // Retirer les rôles métiers (ROLE_ELEVE, ROLE_MEDECIN, ROLE_PARTENAIRE)
        $users = $this->connection->fetchAllAssociative('SELECT id, roles FROM user');

        foreach ($users as $user) {
            $userId = $user['id'];
            $currentRoles = json_decode($user['roles'], true) ?? [];

            // Retirer les rôles métiers
            $currentRoles = array_filter($currentRoles, function($role) {
                return !in_array($role, ['ROLE_ELEVE', 'ROLE_MEDECIN', 'ROLE_PARTENAIRE']);
            });

            $newRoles = json_encode(array_values($currentRoles));
            $this->addSql('UPDATE user SET roles = :roles WHERE id = :id', [
                'roles' => $newRoles,
                'id' => $userId,
            ]);
        }
    }
}
