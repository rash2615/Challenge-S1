<?php
////////////////////////////////////////
/////////////////DEVIS//////////////////
////////////////////////////////////////

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607104344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs        
        $this->addSql('CREATE TABLE devis (id SERIAL NOT NULL, customer_id INT NOT NULL, chrono VARCHAR(13) NOT NULL, status VARCHAR(10) NOT NULL, validity_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, work_start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, work_duration VARCHAR(50) NOT NULL, payment_deadline TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, payment_delay_rate INT DEFAULT NULL, tva_applicable BOOLEAN NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, signed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_draft BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_8b27c52b9395c3f3 ON devis (customer_id)');
        $this->addSql('CREATE INDEX IDX_6D3CB6CB41DEFADA ON invoices_services (devis_id)');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id)');
        $this->addSql('ALTER TABLE invoices_services ADD CONSTRAINT FK_6D3CB6CB41DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP INDEX IDX_6D3CB6CB41DEFADA ON invoices_services');
    }
}
?>
