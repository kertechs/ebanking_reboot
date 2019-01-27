<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127023124 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agences ADD banque_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE agences ADD CONSTRAINT FK_B46015DD7F90B18B FOREIGN KEY (banque_id_id) REFERENCES banques (id)');
        $this->addSql('CREATE INDEX IDX_B46015DD7F90B18B ON agences (banque_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agences DROP FOREIGN KEY FK_B46015DD7F90B18B');
        $this->addSql('DROP INDEX IDX_B46015DD7F90B18B ON agences');
        $this->addSql('ALTER TABLE agences DROP banque_id_id');
    }
}
