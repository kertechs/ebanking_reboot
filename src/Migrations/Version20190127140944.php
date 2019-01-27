<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127140944 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comptes_client (comptes_id INT NOT NULL, client_id INT NOT NULL, INDEX IDX_28C9A899DCED588B (comptes_id), INDEX IDX_28C9A89919EB6921 (client_id), PRIMARY KEY(comptes_id, client_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comptes_client ADD CONSTRAINT FK_28C9A899DCED588B FOREIGN KEY (comptes_id) REFERENCES comptes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comptes_client ADD CONSTRAINT FK_28C9A89919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE comptes_client');
    }
}
