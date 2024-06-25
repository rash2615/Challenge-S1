<?php
////////////////////////////////////////
///////////////CUSTOMERS////////////////
////////////////////////////////////////

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607104342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customers (
            id SERIAL NOT NULL, 
            owner_id INT NOT NULL, 
            firstname VARCHAR(30) DEFAULT NULL, 
            lastname VARCHAR(30) DEFAULT NULL, 
            email VARCHAR(255) NOT NULL, 
            company VARCHAR(40) DEFAULT NULL, 
            type VARCHAR(7) NOT NULL, 
            phone VARCHAR(30) DEFAULT NULL, 
            siret BIGINT DEFAULT NULL, 
            address VARCHAR(255) NOT NULL, 
            postal_code INT NOT NULL, 
            city VARCHAR(255) NOT NULL, 
            country VARCHAR(3) NOT NULL, 
            picture VARCHAR(255) DEFAULT NULL, 
            created_at TIMESTAMP NOT NULL, 
            updated_at TIMESTAMP DEFAULT NULL, 
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_62534E217E3C61F9 ON customers (owner_id)');
        $this->addSql('ALTER TABLE customers ADD CONSTRAINT FK_62534E217E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE customers');
    }
}
?>
