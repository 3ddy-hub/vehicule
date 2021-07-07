<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210707065316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE modele (id INT AUTO_INCREMENT NOT NULL, modele VARCHAR(15) NOT NULL, marque VARCHAR(15) NOT NULL, puissance VARCHAR(15) NOT NULL, carburant VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proprietaire (id INT AUTO_INCREMENT NOT NULL, voiture_id INT DEFAULT NULL, nom VARCHAR(15) NOT NULL, prenom VARCHAR(15) NOT NULL, addresse VARCHAR(50) NOT NULL, code_postal INT NOT NULL, ville VARCHAR(15) NOT NULL, tel INT NOT NULL, INDEX IDX_69E399D6181A8BA (voiture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE voiture (id INT AUTO_INCREMENT NOT NULL, modele_id INT DEFAULT NULL, immatriculation VARCHAR(20) NOT NULL, couleur VARCHAR(15) NOT NULL, kilometrage INT NOT NULL, INDEX IDX_E9E2810FAC14B70A (modele_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE proprietaire ADD CONSTRAINT FK_69E399D6181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810FAC14B70A FOREIGN KEY (modele_id) REFERENCES modele (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE voiture DROP FOREIGN KEY FK_E9E2810FAC14B70A');
        $this->addSql('ALTER TABLE proprietaire DROP FOREIGN KEY FK_69E399D6181A8BA');
        $this->addSql('DROP TABLE modele');
        $this->addSql('DROP TABLE proprietaire');
        $this->addSql('DROP TABLE voiture');
    }
}
