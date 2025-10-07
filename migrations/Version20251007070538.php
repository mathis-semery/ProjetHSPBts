<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251007070538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE canal (id INT AUTO_INCREMENT NOT NULL, ref_user_id INT NOT NULL, nom VARCHAR(100) NOT NULL, description VARCHAR(500) DEFAULT NULL, liste_auto VARCHAR(255) NOT NULL, INDEX IDX_E181FB59637A8045 (ref_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidature (id INT AUTO_INCREMENT NOT NULL, lettre VARCHAR(255) NOT NULL, etat TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, site_web VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D19FA60E1540971 (site_web), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etablissement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, adresse VARCHAR(150) NOT NULL, site_web VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_20FD592CE1540971 (site_web), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(100) NOT NULL, titre VARCHAR(100) NOT NULL, description VARCHAR(500) DEFAULT NULL, adresse VARCHAR(125) DEFAULT NULL, nombre_de_place INT DEFAULT NULL, element_requis VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hopital (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, commune VARCHAR(150) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, ref_user_id INT NOT NULL, ref_evenement_id INT NOT NULL, etat TINYINT(1) DEFAULT NULL, date DATETIME NOT NULL, INDEX IDX_5E90F6D6637A8045 (ref_user_id), INDEX IDX_5E90F6D6EB24E9E0 (ref_evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(500) NOT NULL, mission VARCHAR(350) DEFAULT NULL, type VARCHAR(255) NOT NULL, salaire NUMERIC(10, 0) NOT NULL, etat TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, ref_canal_id INT NOT NULL, ref_user_id INT NOT NULL, titre VARCHAR(255) NOT NULL, texte VARCHAR(255) NOT NULL, date_heure DATETIME NOT NULL, INDEX IDX_5A8A6C8DEECC9DA9 (ref_canal_id), INDEX IDX_5A8A6C8D637A8045 (ref_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, ref_post_id INT NOT NULL, ref_user_id INT NOT NULL, texte VARCHAR(255) NOT NULL, date_heure DATETIME NOT NULL, INDEX IDX_5FB6DEC78F9D50FC (ref_post_id), INDEX IDX_5FB6DEC7637A8045 (ref_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, ref_hopital_id INT DEFAULT NULL, ref_etablissement_id INT DEFAULT NULL, ref_entreprise_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, metier VARCHAR(255) NOT NULL, etat_validation TINYINT(1) DEFAULT NULL, date_creation DATE NOT NULL, formation VARCHAR(255) DEFAULT NULL, cv VARCHAR(255) DEFAULT NULL, specialite VARCHAR(255) DEFAULT NULL, poste_occupe VARCHAR(100) DEFAULT NULL, verification_token VARCHAR(255) DEFAULT NULL, INDEX IDX_8D93D649CACA508C (ref_hopital_id), INDEX IDX_8D93D6492925434B (ref_etablissement_id), INDEX IDX_8D93D64980FEF88A (ref_entreprise_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE canal ADD CONSTRAINT FK_E181FB59637A8045 FOREIGN KEY (ref_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6637A8045 FOREIGN KEY (ref_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6EB24E9E0 FOREIGN KEY (ref_evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DEECC9DA9 FOREIGN KEY (ref_canal_id) REFERENCES canal (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D637A8045 FOREIGN KEY (ref_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC78F9D50FC FOREIGN KEY (ref_post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7637A8045 FOREIGN KEY (ref_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649CACA508C FOREIGN KEY (ref_hopital_id) REFERENCES hopital (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6492925434B FOREIGN KEY (ref_etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64980FEF88A FOREIGN KEY (ref_entreprise_id) REFERENCES etablissement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE canal DROP FOREIGN KEY FK_E181FB59637A8045');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6637A8045');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D6EB24E9E0');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DEECC9DA9');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D637A8045');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC78F9D50FC');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7637A8045');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649CACA508C');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6492925434B');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64980FEF88A');
        $this->addSql('DROP TABLE canal');
        $this->addSql('DROP TABLE candidature');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE etablissement');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE hopital');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE offre');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
