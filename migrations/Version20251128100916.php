<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251128100916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE canal_user DROP FOREIGN KEY FK_1340295468DB5B2E');
        $this->addSql('ALTER TABLE canal_user DROP FOREIGN KEY FK_13402954A76ED395');
        $this->addSql('DROP TABLE canal_user');
        $this->addSql('ALTER TABLE canal CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE liste_auto liste_auto JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE offre DROP date_creation, CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DEECC9DA9');
        $this->addSql('DROP INDEX IDX_5A8A6C8DEECC9DA9 ON post');
        $this->addSql('ALTER TABLE post CHANGE titre titre VARCHAR(255) NOT NULL, CHANGE texte texte LONGTEXT NOT NULL, CHANGE ref_canal_id canal_id INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D68DB5B2E FOREIGN KEY (canal_id) REFERENCES canal (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D68DB5B2E ON post (canal_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A4AEAFEA');
        $this->addSql('DROP INDEX IDX_8D93D649A4AEAFEA ON user');
        $this->addSql('ALTER TABLE user CHANGE entreprise_id ref_entreprise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64980FEF88A FOREIGN KEY (ref_entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64980FEF88A ON user (ref_entreprise_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE canal_user (canal_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_1340295468DB5B2E (canal_id), INDEX IDX_13402954A76ED395 (user_id), PRIMARY KEY(canal_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE canal_user ADD CONSTRAINT FK_1340295468DB5B2E FOREIGN KEY (canal_id) REFERENCES canal (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE canal_user ADD CONSTRAINT FK_13402954A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE canal CHANGE nom nom VARCHAR(100) NOT NULL, CHANGE description description VARCHAR(500) DEFAULT NULL, CHANGE liste_auto liste_auto VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE offre ADD date_creation DATETIME NOT NULL, CHANGE type type VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D68DB5B2E');
        $this->addSql('DROP INDEX IDX_5A8A6C8D68DB5B2E ON post');
        $this->addSql('ALTER TABLE post CHANGE titre titre VARCHAR(255) DEFAULT NULL, CHANGE texte texte VARCHAR(1000) NOT NULL, CHANGE canal_id ref_canal_id INT NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DEECC9DA9 FOREIGN KEY (ref_canal_id) REFERENCES canal (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DEECC9DA9 ON post (ref_canal_id)');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64980FEF88A');
        $this->addSql('DROP INDEX IDX_8D93D64980FEF88A ON `user`');
        $this->addSql('ALTER TABLE `user` CHANGE ref_entreprise_id entreprise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D649A4AEAFEA ON `user` (entreprise_id)');
    }
}
