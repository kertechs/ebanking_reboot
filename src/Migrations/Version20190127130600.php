<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127130600 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE banques ADD banque_compte_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE banques ADD CONSTRAINT FK_34D0454760497E77 FOREIGN KEY (banque_compte_id_id) REFERENCES comptes (id)');
        $this->addSql('CREATE INDEX IDX_34D0454760497E77 ON banques (banque_compte_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE banques DROP FOREIGN KEY FK_34D0454760497E77');
        $this->addSql('DROP INDEX IDX_34D0454760497E77 ON banques');
        $this->addSql('ALTER TABLE banques DROP banque_compte_id_id');
    }
}
