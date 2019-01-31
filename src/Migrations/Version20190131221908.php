<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190131221908 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operations ADD compte_destinataire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operations ADD CONSTRAINT FK_281453486D34063E FOREIGN KEY (compte_destinataire_id) REFERENCES comptes (id)');
        $this->addSql('CREATE INDEX IDX_281453486D34063E ON operations (compte_destinataire_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operations DROP FOREIGN KEY FK_281453486D34063E');
        $this->addSql('DROP INDEX IDX_281453486D34063E ON operations');
        $this->addSql('ALTER TABLE operations DROP compte_destinataire_id');
    }
}
