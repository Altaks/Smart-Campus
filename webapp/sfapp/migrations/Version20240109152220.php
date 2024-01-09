<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240109152220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demande_travaux (id INT AUTO_INCREMENT NOT NULL, salle_id INT NOT NULL, systeme_acquisition_id INT DEFAULT NULL, type VARCHAR(12) NOT NULL, terminee TINYINT(1) NOT NULL, date DATETIME NOT NULL, INDEX IDX_5CD34834DC304035 (salle_id), INDEX IDX_5CD348343572D180 (systeme_acquisition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, systeme_acquisition_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, orientation VARCHAR(2) NOT NULL, nombre_fenetre INT NOT NULL, nombre_porte INT NOT NULL, contient_pc TINYINT(1) NOT NULL, batiment VARCHAR(30) DEFAULT NULL, UNIQUE INDEX UNIQ_4E977E5C3572D180 (systeme_acquisition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE systeme_acquisition (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(7) NOT NULL, base_donnees VARCHAR(12) NOT NULL, etat VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5F4381C96C6E55B5 (nom), UNIQUE INDEX UNIQ_5F4381C94D504CD0 (base_donnees), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, identifiant VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demande_travaux ADD CONSTRAINT FK_5CD34834DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE demande_travaux ADD CONSTRAINT FK_5CD348343572D180 FOREIGN KEY (systeme_acquisition_id) REFERENCES systeme_acquisition (id)');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5C3572D180 FOREIGN KEY (systeme_acquisition_id) REFERENCES systeme_acquisition (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_travaux DROP FOREIGN KEY FK_5CD34834DC304035');
        $this->addSql('ALTER TABLE demande_travaux DROP FOREIGN KEY FK_5CD348343572D180');
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5C3572D180');
        $this->addSql('DROP TABLE demande_travaux');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE systeme_acquisition');
        $this->addSql('DROP TABLE utilisateur');
    }
}
