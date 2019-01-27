<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127024846 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comptes_clients_comptes (comptes_clients_id INT NOT NULL, comptes_id INT NOT NULL, INDEX IDX_C5DAD8F4F960AA47 (comptes_clients_id), INDEX IDX_C5DAD8F4DCED588B (comptes_id), PRIMARY KEY(comptes_clients_id, comptes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comptes_clients_client (comptes_clients_id INT NOT NULL, client_id INT NOT NULL, INDEX IDX_673B6CD8F960AA47 (comptes_clients_id), INDEX IDX_673B6CD819EB6921 (client_id), PRIMARY KEY(comptes_clients_id, client_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comptes_clients_comptes ADD CONSTRAINT FK_C5DAD8F4F960AA47 FOREIGN KEY (comptes_clients_id) REFERENCES comptes_clients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comptes_clients_comptes ADD CONSTRAINT FK_C5DAD8F4DCED588B FOREIGN KEY (comptes_id) REFERENCES comptes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comptes_clients_client ADD CONSTRAINT FK_673B6CD8F960AA47 FOREIGN KEY (comptes_clients_id) REFERENCES comptes_clients (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comptes_clients_client ADD CONSTRAINT FK_673B6CD819EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE comptes_clients_comptes');
        $this->addSql('DROP TABLE comptes_clients_client');
    }
}
