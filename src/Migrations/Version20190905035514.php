<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190905035514 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_108C6A8F5F37A13B ON affiliates (token)');
        $this->addSql('ALTER TABLE affiliates_categories DROP FOREIGN KEY affiliates_categories_ibfk_1');
        $this->addSql('ALTER TABLE affiliates_categories DROP FOREIGN KEY affiliates_categories_ibfk_2');
        $this->addSql('ALTER TABLE affiliates_categories ADD PRIMARY KEY (affiliate_id, category_id)');
        $this->addSql('ALTER TABLE affiliates_categories ADD CONSTRAINT FK_87BE21809F12C49A FOREIGN KEY (affiliate_id) REFERENCES affiliates (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE affiliates_categories ADD CONSTRAINT FK_87BE218012469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE affiliates_categories RENAME INDEX affiliate_id TO IDX_87BE21809F12C49A');
        $this->addSql('ALTER TABLE affiliates_categories RENAME INDEX category_id TO IDX_87BE218012469DE2');
        $this->addSql('ALTER TABLE jobs DROP FOREIGN KEY jobs_ibfk_1');
        $this->addSql('ALTER TABLE jobs ADD token VARCHAR(255) NOT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE url url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE jobs ADD CONSTRAINT FK_A8936DC512469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8936DC55F37A13B ON jobs (token)');
        $this->addSql('ALTER TABLE jobs RENAME INDEX category_id TO IDX_A8936DC512469DE2');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_108C6A8F5F37A13B ON affiliates');
        $this->addSql('ALTER TABLE affiliates_categories DROP FOREIGN KEY FK_87BE21809F12C49A');
        $this->addSql('ALTER TABLE affiliates_categories DROP FOREIGN KEY FK_87BE218012469DE2');
        $this->addSql('ALTER TABLE affiliates_categories DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE affiliates_categories ADD CONSTRAINT affiliates_categories_ibfk_1 FOREIGN KEY (category_id) REFERENCES categories (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE affiliates_categories ADD CONSTRAINT affiliates_categories_ibfk_2 FOREIGN KEY (affiliate_id) REFERENCES affiliates (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE affiliates_categories RENAME INDEX idx_87be218012469de2 TO category_id');
        $this->addSql('ALTER TABLE affiliates_categories RENAME INDEX idx_87be21809f12c49a TO affiliate_id');
        $this->addSql('ALTER TABLE jobs DROP FOREIGN KEY FK_A8936DC512469DE2');
        $this->addSql('DROP INDEX UNIQ_A8936DC55F37A13B ON jobs');
        $this->addSql('ALTER TABLE jobs DROP token, CHANGE logo logo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE jobs ADD CONSTRAINT jobs_ibfk_1 FOREIGN KEY (category_id) REFERENCES categories (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE jobs RENAME INDEX idx_a8936dc512469de2 TO category_id');
    }
}
