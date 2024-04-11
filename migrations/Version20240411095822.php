<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411095822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP qte, DROP timbre');
        $this->addSql('ALTER TABLE commande_materiel ADD remise DOUBLE PRECISION DEFAULT NULL, ADD timbre DOUBLE PRECISION DEFAULT NULL, ADD qte INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD qte INT NOT NULL, ADD timbre DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE commande_materiel DROP remise, DROP timbre, DROP qte');
    }
}
