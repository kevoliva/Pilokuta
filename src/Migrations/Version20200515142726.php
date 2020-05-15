<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200515142726 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE creneau DROP FOREIGN KEY FK_F9668B5FA9E2D76C');
        $this->addSql('ALTER TABLE joueur_equipe DROP FOREIGN KEY FK_CDF2AA99A9E2D76C');
        $this->addSql('DROP TABLE joueur');
        $this->addSql('DROP TABLE joueur_equipe');
        $this->addSql('DROP INDEX IDX_F9668B5FA9E2D76C ON creneau');
        $this->addSql('ALTER TABLE creneau DROP joueur_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE joueur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, telephone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE joueur_equipe (joueur_id INT NOT NULL, equipe_id INT NOT NULL, INDEX IDX_CDF2AA996D861B89 (equipe_id), INDEX IDX_CDF2AA99A9E2D76C (joueur_id), PRIMARY KEY(joueur_id, equipe_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE joueur_equipe ADD CONSTRAINT FK_CDF2AA996D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE joueur_equipe ADD CONSTRAINT FK_CDF2AA99A9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE creneau ADD joueur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE creneau ADD CONSTRAINT FK_F9668B5FA9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueur (id)');
        $this->addSql('CREATE INDEX IDX_F9668B5FA9E2D76C ON creneau (joueur_id)');
    }
}
