<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127102647 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comptes ADD agence_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comptes ADD CONSTRAINT FK_56735801D1F6E7C3 FOREIGN KEY (agence_id_id) REFERENCES agences (id)');
        $this->addSql('CREATE INDEX IDX_56735801D1F6E7C3 ON comptes (agence_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comptes DROP FOREIGN KEY FK_56735801D1F6E7C3');
        $this->addSql('DROP INDEX IDX_56735801D1F6E7C3 ON comptes');
        $this->addSql('ALTER TABLE comptes DROP agence_id_id, CHANGE num_compte num_compte INT UNSIGNED DEFAULT NULL');
    }
}
