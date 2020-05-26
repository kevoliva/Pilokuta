<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200526145228 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE creneau (id INT AUTO_INCREMENT NOT NULL, tournoi_id INT DEFAULT NULL, user_id INT DEFAULT NULL, la_date DATETIME DEFAULT NULL, duree INT DEFAULT NULL, commentaire VARCHAR(255) DEFAULT NULL, INDEX IDX_F9668B5FF607770A (tournoi_id), INDEX IDX_F9668B5FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe (id INT AUTO_INCREMENT NOT NULL, poule_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_2449BA1526596FD8 (poule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe_partie (equipe_id INT NOT NULL, partie_id INT NOT NULL, INDEX IDX_8AC79956D861B89 (equipe_id), INDEX IDX_8AC7995E075F7A4 (partie_id), PRIMARY KEY(equipe_id, partie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partie (id INT AUTO_INCREMENT NOT NULL, creneau_id INT DEFAULT NULL, score_equipe1 INT DEFAULT NULL, score_equipe2 INT DEFAULT NULL, UNIQUE INDEX UNIQ_59B1F3D7D0729A9 (creneau_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poule (id INT AUTO_INCREMENT NOT NULL, serie_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_FA1FEB40D94388BD (serie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie (id INT AUTO_INCREMENT NOT NULL, tournoi_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, INDEX IDX_AA3A9334F607770A (tournoi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournoi (id INT AUTO_INCREMENT NOT NULL, date_debut DATE DEFAULT NULL, date_fin DATE DEFAULT NULL, nb_joueurs_par_equipe INT DEFAULT NULL, etat VARCHAR(255) DEFAULT NULL, libelle VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_equipe (user_id INT NOT NULL, equipe_id INT NOT NULL, INDEX IDX_411BA128A76ED395 (user_id), INDEX IDX_411BA1286D861B89 (equipe_id), PRIMARY KEY(user_id, equipe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE creneau ADD CONSTRAINT FK_F9668B5FF607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id)');
        $this->addSql('ALTER TABLE creneau ADD CONSTRAINT FK_F9668B5FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA1526596FD8 FOREIGN KEY (poule_id) REFERENCES poule (id)');
        $this->addSql('ALTER TABLE equipe_partie ADD CONSTRAINT FK_8AC79956D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_partie ADD CONSTRAINT FK_8AC7995E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3D7D0729A9 FOREIGN KEY (creneau_id) REFERENCES creneau (id)');
        $this->addSql('ALTER TABLE poule ADD CONSTRAINT FK_FA1FEB40D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id)');
        $this->addSql('ALTER TABLE serie ADD CONSTRAINT FK_AA3A9334F607770A FOREIGN KEY (tournoi_id) REFERENCES tournoi (id)');
        $this->addSql('ALTER TABLE user_equipe ADD CONSTRAINT FK_411BA128A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_equipe ADD CONSTRAINT FK_411BA1286D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3D7D0729A9');
        $this->addSql('ALTER TABLE equipe_partie DROP FOREIGN KEY FK_8AC79956D861B89');
        $this->addSql('ALTER TABLE user_equipe DROP FOREIGN KEY FK_411BA1286D861B89');
        $this->addSql('ALTER TABLE equipe_partie DROP FOREIGN KEY FK_8AC7995E075F7A4');
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA1526596FD8');
        $this->addSql('ALTER TABLE poule DROP FOREIGN KEY FK_FA1FEB40D94388BD');
        $this->addSql('ALTER TABLE creneau DROP FOREIGN KEY FK_F9668B5FF607770A');
        $this->addSql('ALTER TABLE serie DROP FOREIGN KEY FK_AA3A9334F607770A');
        $this->addSql('ALTER TABLE creneau DROP FOREIGN KEY FK_F9668B5FA76ED395');
        $this->addSql('ALTER TABLE user_equipe DROP FOREIGN KEY FK_411BA128A76ED395');
        $this->addSql('DROP TABLE creneau');
        $this->addSql('DROP TABLE equipe');
        $this->addSql('DROP TABLE equipe_partie');
        $this->addSql('DROP TABLE partie');
        $this->addSql('DROP TABLE poule');
        $this->addSql('DROP TABLE serie');
        $this->addSql('DROP TABLE tournoi');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_equipe');
    }
}
