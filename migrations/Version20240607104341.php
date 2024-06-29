<?php
////////////////////////////////////////
////////////////USERS///////////////////
////////////////////////////////////////

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607104341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id SERIAL NOT NULL, users_id INT DEFAULT NULL, nomProduit VARCHAR(45) NOT NULL, description VARCHAR(255) NOT NULL, caracteristique TEXT NOT NULL, categorie VARCHAR(45) NOT NULL, prix_ht NUMERIC(7, 2) NOT NULL, prix_ttc NUMERIC(7, 2) NOT NULL, "created_at" timestamp, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D34A04AD67B3B43D ON product (users_id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_USERS FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE NO ACTION');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE users');
    }
}
?>
