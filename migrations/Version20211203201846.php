<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211203201846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX tel ON client');
        $this->addSql('DROP INDEX cin ON client');
        $this->addSql('DROP INDEX email ON client');
        $this->addSql('DROP INDEX reference ON facture');
        $this->addSql('DROP INDEX tel ON fournisseur');
        $this->addSql('DROP INDEX email ON fournisseur');
        $this->addSql('DROP INDEX cin ON fournisseur');
        $this->addSql('DROP INDEX cin ON user');
        $this->addSql('DROP INDEX tel ON user');
        $this->addSql('DROP INDEX email ON user');
        $this->addSql('ALTER TABLE user DROP date');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX tel ON client (tel)');
        $this->addSql('CREATE UNIQUE INDEX cin ON client (cin)');
        $this->addSql('CREATE UNIQUE INDEX email ON client (email)');
        $this->addSql('CREATE UNIQUE INDEX reference ON facture (reference)');
        $this->addSql('CREATE UNIQUE INDEX tel ON fournisseur (tel)');
        $this->addSql('CREATE UNIQUE INDEX email ON fournisseur (email)');
        $this->addSql('CREATE UNIQUE INDEX cin ON fournisseur (cin)');
        $this->addSql('ALTER TABLE user ADD date DATE DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX cin ON user (cin)');
        $this->addSql('CREATE UNIQUE INDEX tel ON user (tel)');
        $this->addSql('CREATE UNIQUE INDEX email ON user (email)');
    }
}
