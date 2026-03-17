<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251128155236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        // 1. Ajouter les colonnes comme nullable d'abord
        $this->addSql('ALTER TABLE offre ADD auteur_id INT DEFAULT NULL, ADD titre VARCHAR(255) DEFAULT NULL, CHANGE salaire salaire NUMERIC(10, 0) DEFAULT NULL');

        // 2. Affecter des valeurs par défaut aux offres existantes
        // Récupérer le premier utilisateur avec ROLE_PARTENAIRE ou ROLE_ADMIN
        $this->addSql('UPDATE offre o SET o.auteur_id = (SELECT id FROM `user` WHERE JSON_CONTAINS(roles, \'["ROLE_PARTENAIRE"]\') OR JSON_CONTAINS(roles, \'["ROLE_ADMIN"]\') LIMIT 1) WHERE o.auteur_id IS NULL');
        $this->addSql('UPDATE offre SET titre = CONCAT("Offre ", type) WHERE titre IS NULL');

        // 3. Rendre les colonnes NOT NULL et ajouter les contraintes
        $this->addSql('ALTER TABLE offre MODIFY auteur_id INT NOT NULL, MODIFY titre VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_AF86866F60BB6FE6 ON offre (auteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F60BB6FE6');
        $this->addSql('DROP INDEX IDX_AF86866F60BB6FE6 ON offre');
        $this->addSql('ALTER TABLE offre DROP auteur_id, DROP titre, CHANGE salaire salaire NUMERIC(10, 0) NOT NULL');
    }
}
