<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240415131759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, remise DOUBLE PRECISION DEFAULT \'0\', timbre DOUBLE PRECISION DEFAULT \'0\', date DATE NOT NULL, UNIQUE INDEX UNIQ_FE86641077153098 (code), INDEX IDX_FE86641019EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture_materiel (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, materiel_id INT DEFAULT NULL, prix DOUBLE PRECISION DEFAULT NULL, qte INT NOT NULL, tva DOUBLE PRECISION DEFAULT \'0\', remise DOUBLE PRECISION DEFAULT \'0\', INDEX IDX_A2DBE607F2DEE08 (facture_id), INDEX IDX_A2DBE6016880AAF (materiel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641019EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE facture_materiel ADD CONSTRAINT FK_A2DBE607F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE facture_materiel ADD CONSTRAINT FK_A2DBE6016880AAF FOREIGN KEY (materiel_id) REFERENCES materiel (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE86641019EB6921');
        $this->addSql('ALTER TABLE facture_materiel DROP FOREIGN KEY FK_A2DBE607F2DEE08');
        $this->addSql('ALTER TABLE facture_materiel DROP FOREIGN KEY FK_A2DBE6016880AAF');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE facture_materiel');
    }
}
