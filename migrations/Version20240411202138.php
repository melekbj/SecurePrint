<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411202138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE ttva ttva DOUBLE PRECISION DEFAULT \'0\', CHANGE remise remise DOUBLE PRECISION DEFAULT \'0\', CHANGE timbre timbre DOUBLE PRECISION DEFAULT \'0\'');
        $this->addSql('ALTER TABLE commande_materiel CHANGE tva tva DOUBLE PRECISION DEFAULT \'0\', CHANGE remise remise DOUBLE PRECISION DEFAULT \'0\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE ttva ttva DOUBLE PRECISION DEFAULT NULL, CHANGE remise remise DOUBLE PRECISION DEFAULT NULL, CHANGE timbre timbre DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE commande_materiel CHANGE tva tva DOUBLE PRECISION DEFAULT NULL, CHANGE remise remise DOUBLE PRECISION DEFAULT NULL');
    }
}
