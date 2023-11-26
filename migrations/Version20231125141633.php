<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231125141633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF346686C8A81A9');
        $this->addSql('DROP INDEX IDX_3AF346686C8A81A9 ON categories');
        $this->addSql('ALTER TABLE categories DROP products_id');
        $this->addSql('ALTER TABLE products ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5ABCF5E72D FOREIGN KEY (categorie_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5ABCF5E72D ON products (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories ADD products_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF346686C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3AF346686C8A81A9 ON categories (products_id)');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5ABCF5E72D');
        $this->addSql('DROP INDEX IDX_B3BA5A5ABCF5E72D ON products');
        $this->addSql('ALTER TABLE products DROP categorie_id');
    }
}
