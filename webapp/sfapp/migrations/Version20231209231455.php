<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231209231455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE batiment (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, departement VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE releve (id INT AUTO_INCREMENT NOT NULL, systeme_acquisition_id INT DEFAULT NULL, salle_id INT NOT NULL, temperature DOUBLE PRECISION DEFAULT NULL, humidite INT DEFAULT NULL, qualite_air INT DEFAULT NULL, horodatage DATETIME NOT NULL, INDEX IDX_DDABFF833572D180 (systeme_acquisition_id), INDEX IDX_DDABFF83DC304035 (salle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, batiment_id INT NOT NULL, systeme_acquisition_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, etage INT NOT NULL, orientation VARCHAR(2) NOT NULL, nombre_fenetre INT NOT NULL, nombre_porte INT NOT NULL, contient_pc TINYINT(1) NOT NULL, INDEX IDX_4E977E5CD6F6891B (batiment_id), UNIQUE INDEX UNIQ_4E977E5C3572D180 (systeme_acquisition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE systeme_acquisition (id INT AUTO_INCREMENT NOT NULL, adresse_mac VARCHAR(17) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE releve ADD CONSTRAINT FK_DDABFF833572D180 FOREIGN KEY (systeme_acquisition_id) REFERENCES systeme_acquisition (id)');
        $this->addSql('ALTER TABLE releve ADD CONSTRAINT FK_DDABFF83DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5CD6F6891B FOREIGN KEY (batiment_id) REFERENCES batiment (id)');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5C3572D180 FOREIGN KEY (systeme_acquisition_id) REFERENCES systeme_acquisition (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE releve DROP FOREIGN KEY FK_DDABFF833572D180');
        $this->addSql('ALTER TABLE releve DROP FOREIGN KEY FK_DDABFF83DC304035');
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5CD6F6891B');
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5C3572D180');
        $this->addSql('DROP TABLE batiment');
        $this->addSql('DROP TABLE releve');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE systeme_acquisition');
    }
}
