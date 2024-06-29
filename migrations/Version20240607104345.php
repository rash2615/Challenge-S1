<?php
////////////////////////////////////////
///////////////INVOICES/////////////////
////////////////////////////////////////

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607104345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoices (id SERIAL NOT NULL, customer_id INT NOT NULL, status VARCHAR(9) NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, chrono VARCHAR(13) NOT NULL, tva_applicable BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, service_done_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, payment_deadline TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, payment_delay_rate INT DEFAULT NULL, is_draft BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_6a2f2f959395c3f3 ON invoices (customer_id)');
        $this->addSql('ALTER TABLE invoices ADD CONSTRAINT FK_6A2F2F959395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE invoices');
    }
}
?>
