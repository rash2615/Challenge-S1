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
        $this->addSql('CREATE TABLE users (
            id SERIAL NOT NULL, 
            firstname VARCHAR(255) NOT NULL, 
            lastname VARCHAR(255) NOT NULL, 
            email VARCHAR(255) NOT NULL, 
            password VARCHAR(255) NOT NULL, 
            roles JSON NOT NULL, 
            confirmation_token VARCHAR(255) DEFAULT NULL, 
            confirmed_at TIMESTAMP DEFAULT NULL, 
            phone VARCHAR(255) DEFAULT NULL, 
            business_name VARCHAR(40) DEFAULT NULL, 
            siret BIGINT NOT NULL, 
            tva_number VARCHAR(13) DEFAULT NULL, 
            address VARCHAR(255) NOT NULL, 
            city VARCHAR(255) NOT NULL, 
            postal_code INT NOT NULL, 
            country VARCHAR(3) NOT NULL, 
            created_at TIMESTAMP NOT NULL, 
            updated_at TIMESTAMP DEFAULT NULL, 
            UNIQUE (email), 
            PRIMARY KEY(id)
        )');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE users');
    }
}
?>
