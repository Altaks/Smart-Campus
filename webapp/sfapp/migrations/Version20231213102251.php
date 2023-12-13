<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231213102251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE releve DROP FOREIGN KEY FK_DDABFF833572D180');
        $this->addSql('ALTER TABLE releve DROP FOREIGN KEY FK_DDABFF83DC304035');
        $this->addSql('DROP TABLE releve');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, identifiant VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mot_de_passe VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE releve (id INT AUTO_INCREMENT NOT NULL, systeme_acquisition_id INT DEFAULT NULL, salle_id INT NOT NULL, temperature DOUBLE PRECISION DEFAULT NULL, humidite INT DEFAULT NULL, qualite_air INT DEFAULT NULL, horodatage DATETIME NOT NULL, INDEX IDX_DDABFF833572D180 (systeme_acquisition_id), INDEX IDX_DDABFF83DC304035 (salle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE releve ADD CONSTRAINT FK_DDABFF833572D180 FOREIGN KEY (systeme_acquisition_id) REFERENCES systeme_acquisition (id)');
        $this->addSql('ALTER TABLE releve ADD CONSTRAINT FK_DDABFF83DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
    }
}
