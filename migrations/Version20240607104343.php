<?php
////////////////////////////////////////
///////////INVOICES_SERVICES////////////
////////////////////////////////////////

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240607104343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoices_services (
            id SERIAL NOT NULL, 
            invoice_id INT DEFAULT NULL, 
            name VARCHAR(60) NOT NULL, 
            description VARCHAR(255) DEFAULT NULL, 
            quantity INT DEFAULT NULL, 
            unit_price DOUBLE PRECISION NOT NULL, 
            devis_id INT DEFAULT NULL, 
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_6D3CB6CB2989F1FD ON invoices_services (invoice_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE invoices_services');
    }
}
?>
