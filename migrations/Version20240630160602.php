<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240630160602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "invoices" ADD "users_id" integer NOT NULL');
        $this->addSql('ALTER TABLE "invoices" ADD FOREIGN KEY ("users_id") REFERENCES "users" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
        $this->addSql('ALTER TABLE "devis" ADD "users_id" integer NOT NULL');
        $this->addSql('ALTER TABLE "devis" ADD FOREIGN KEY ("users_id") REFERENCES "users" ("id") ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "invoices" DROP CONSTRAINT "invoices_user_fkey"');
        $this->addSql('ALTER TABLE "invoices" DROP "users_id"');
        $this->addSql('ALTER TABLE "devis" DROP CONSTRAINT "invoices_user_fkey"');
        $this->addSql('ALTER TABLE "devis" DROP "users_id"');
    }
}
