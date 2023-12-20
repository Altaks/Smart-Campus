<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231220094444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5CD6F6891B');
        $this->addSql('CREATE TABLE demande_travaux (id INT AUTO_INCREMENT NOT NULL, salle_id INT NOT NULL, systeme_acquisition_id INT DEFAULT NULL, type VARCHAR(12) NOT NULL, terminee TINYINT(1) NOT NULL, date DATETIME NOT NULL, INDEX IDX_5CD34834DC304035 (salle_id), INDEX IDX_5CD348343572D180 (systeme_acquisition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demande_travaux ADD CONSTRAINT FK_5CD34834DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE demande_travaux ADD CONSTRAINT FK_5CD348343572D180 FOREIGN KEY (systeme_acquisition_id) REFERENCES systeme_acquisition (id)');
        $this->addSql('DROP TABLE batiment');
        $this->addSql('DROP INDEX IDX_4E977E5CD6F6891B ON salle');
        $this->addSql('ALTER TABLE salle ADD batiment VARCHAR(30) DEFAULT NULL, DROP batiment_id, DROP etage');
        $this->addSql('ALTER TABLE systeme_acquisition ADD nom VARCHAR(7) NOT NULL, ADD base_donnees VARCHAR(12) NOT NULL, ADD etat VARCHAR(255) NOT NULL, DROP adresse_mac, DROP tag');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE batiment (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE demande_travaux DROP FOREIGN KEY FK_5CD34834DC304035');
        $this->addSql('ALTER TABLE demande_travaux DROP FOREIGN KEY FK_5CD348343572D180');
        $this->addSql('DROP TABLE demande_travaux');
        $this->addSql('ALTER TABLE systeme_acquisition ADD adresse_mac VARCHAR(17) NOT NULL, ADD tag INT NOT NULL, DROP nom, DROP base_donnees, DROP etat');
        $this->addSql('ALTER TABLE salle ADD batiment_id INT NOT NULL, ADD etage INT NOT NULL, DROP batiment');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5CD6F6891B FOREIGN KEY (batiment_id) REFERENCES batiment (id)');
        $this->addSql('CREATE INDEX IDX_4E977E5CD6F6891B ON salle (batiment_id)');
    }
}
