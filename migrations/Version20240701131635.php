<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701131635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "invoices_token" ( "id" serial NOT NULL, PRIMARY KEY ("id"), "user_id" integer NOT NULL, "invoice_id" integer NULL, "devis_id" integer NULL, "token" character varying(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL)');
        $this->addSql('ALTER TABLE "invoices_token" ADD CONSTRAINT "FK_2D3A3D3A76ED395" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE "invoices_token" ADD CONSTRAINT "FK_2D3A3D3A76ED396" FOREIGN KEY ("invoices_id") REFERENCES "invoices" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE "invoices_token" ADD CONSTRAINT "FK_2D3A3D3A76ED397" FOREIGN KEY ("devis_id") REFERENCES "devis" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "invoices_token" DROP CONSTRAINT "FK_2D3A3D3A76ED395"');
        $this->addSql('ALTER TABLE "invoices_token" DROP CONSTRAINT "FK_2D3A3D3A76ED396"');
        $this->addSql('ALTER TABLE "invoices_token" DROP CONSTRAINT "FK_2D3A3D3A76ED397"');
        $this->addSql('DROP TABLE "invoices_token"');
    }
}
