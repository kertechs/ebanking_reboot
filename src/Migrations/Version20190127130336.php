<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127130336 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agences ADD agence_compte_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE agences ADD CONSTRAINT FK_B46015DD76BC4258 FOREIGN KEY (agence_compte_id_id) REFERENCES comptes (id)');
        $this->addSql('CREATE INDEX IDX_B46015DD76BC4258 ON agences (agence_compte_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agences DROP FOREIGN KEY FK_B46015DD76BC4258');
        $this->addSql('DROP INDEX IDX_B46015DD76BC4258 ON agences');
        $this->addSql('ALTER TABLE agences DROP agence_compte_id_id');
    }
}
