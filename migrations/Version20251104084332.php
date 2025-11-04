<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104084332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE canal_user (canal_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_1340295468DB5B2E (canal_id), INDEX IDX_13402954A76ED395 (user_id), PRIMARY KEY(canal_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE canal_user ADD CONSTRAINT FK_1340295468DB5B2E FOREIGN KEY (canal_id) REFERENCES canal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE canal_user ADD CONSTRAINT FK_13402954A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE canal ADD liste_auto VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE canal_user DROP FOREIGN KEY FK_1340295468DB5B2E');
        $this->addSql('ALTER TABLE canal_user DROP FOREIGN KEY FK_13402954A76ED395');
        $this->addSql('DROP TABLE canal_user');
        $this->addSql('ALTER TABLE canal DROP liste_auto');
    }
}
