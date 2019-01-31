<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190131125550 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operations ADD compte_emetteur_id INT DEFAULT NULL, ADD beneficiaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operations ADD CONSTRAINT FK_2814534855F8A4D8 FOREIGN KEY (compte_emetteur_id) REFERENCES comptes (id)');
        $this->addSql('ALTER TABLE operations ADD CONSTRAINT FK_281453485AF81F68 FOREIGN KEY (beneficiaire_id) REFERENCES beneficiaires (id)');
        $this->addSql('CREATE INDEX IDX_2814534855F8A4D8 ON operations (compte_emetteur_id)');
        $this->addSql('CREATE INDEX IDX_281453485AF81F68 ON operations (beneficiaire_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operations DROP FOREIGN KEY FK_2814534855F8A4D8');
        $this->addSql('ALTER TABLE operations DROP FOREIGN KEY FK_281453485AF81F68');
        $this->addSql('DROP INDEX IDX_2814534855F8A4D8 ON operations');
        $this->addSql('DROP INDEX IDX_281453485AF81F68 ON operations');
        $this->addSql('ALTER TABLE operations DROP compte_emetteur_id, DROP beneficiaire_id');
    }
}
