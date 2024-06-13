<?php
////////////////////////////////////////
///////////users_reset_password/////////
////////////////////////////////////////

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607104346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users_reset_password (
            id SERIAL NOT NULL, 
            user_id INT NOT NULL, 
            token VARCHAR(255) NOT NULL, 
            requested_at TIMESTAMP NOT NULL, 
            expires_at TIMESTAMP NOT NULL, 
            UNIQUE (token), 
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_C720A550A76ED395 ON users_reset_password (user_id)');
        $this->addSql('ALTER TABLE users_reset_password ADD CONSTRAINT FK_C720A550A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users_reset_password');
    }
}
?>
