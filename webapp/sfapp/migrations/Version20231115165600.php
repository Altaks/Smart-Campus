<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231115165600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE batiment (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, batiment_id INT NOT NULL, numero VARCHAR(5) NOT NULL, orientation VARCHAR(2) NOT NULL, nbfenetres INT NOT NULL, nbportes INT NOT NULL, contientpc TINYINT(1) NOT NULL, INDEX IDX_4E977E5CD6F6891B (batiment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE systeme_acquisition (id INT AUTO_INCREMENT NOT NULL, salle_id INT DEFAULT NULL, adresse_mac VARCHAR(17) NOT NULL, UNIQUE INDEX UNIQ_5F4381C9DC304035 (salle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5CD6F6891B FOREIGN KEY (batiment_id) REFERENCES batiment (id)');
        $this->addSql('ALTER TABLE systeme_acquisition ADD CONSTRAINT FK_5F4381C9DC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5CD6F6891B');
        $this->addSql('ALTER TABLE systeme_acquisition DROP FOREIGN KEY FK_5F4381C9DC304035');
        $this->addSql('DROP TABLE batiment');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE systeme_acquisition');
    }
}
