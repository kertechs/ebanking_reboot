<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190131134843 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE chequiers (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, compte_id INT DEFAULT NULL, INDEX IDX_741C4AE819EB6921 (client_id), INDEX IDX_741C4AE8F2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cartes_bancaires (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, compte_id INT DEFAULT NULL, INDEX IDX_B627D99B19EB6921 (client_id), INDEX IDX_B627D99BF2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chequiers ADD CONSTRAINT FK_741C4AE819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE chequiers ADD CONSTRAINT FK_741C4AE8F2C56620 FOREIGN KEY (compte_id) REFERENCES comptes (id)');
        $this->addSql('ALTER TABLE cartes_bancaires ADD CONSTRAINT FK_B627D99B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE cartes_bancaires ADD CONSTRAINT FK_B627D99BF2C56620 FOREIGN KEY (compte_id) REFERENCES comptes (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE chequiers');
        $this->addSql('DROP TABLE cartes_bancaires');
    }
}
