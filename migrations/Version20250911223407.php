<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250911223407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alerts (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, triggered_at DATETIME DEFAULT NULL, is_active TINYINT(1) NOT NULL, conditions JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', metadata JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', related_entity VARCHAR(100) DEFAULT NULL, related_entity_id INT DEFAULT NULL, threshold NUMERIC(10, 2) DEFAULT NULL, `condition` VARCHAR(20) DEFAULT NULL, due_date DATETIME DEFAULT NULL, INDEX IDX_F77AC06BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assemblies (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(255) NOT NULL, date DATETIME NOT NULL, type VARCHAR(10) NOT NULL, status VARCHAR(20) NOT NULL, voting_open TINYINT(1) NOT NULL, resolutions JSON NOT NULL COMMENT \'(DC2Type:json)\', documents JSON NOT NULL COMMENT \'(DC2Type:json)\', description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, video_conference_url VARCHAR(255) DEFAULT NULL, video_conference_enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE assembly_votes (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, assembly_id INT NOT NULL, resolution VARCHAR(255) NOT NULL, vote VARCHAR(20) NOT NULL, voted_at DATETIME NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_77495FB2A76ED395 (user_id), INDEX IDX_77495FB2CA2E7D4C (assembly_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE documents (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, filename VARCHAR(255) NOT NULL, file_path VARCHAR(255) NOT NULL, mime_type VARCHAR(20) NOT NULL, file_size INT NOT NULL, uploaded_at DATETIME NOT NULL, document_date DATETIME DEFAULT NULL, related_entity VARCHAR(100) DEFAULT NULL, related_entity_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, is_public TINYINT(1) NOT NULL, metadata JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_A2B07288A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, from_sender VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, type VARCHAR(50) NOT NULL, metadata JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', related_entity VARCHAR(100) DEFAULT NULL, related_entity_id INT DEFAULT NULL, is_important TINYINT(1) NOT NULL, INDEX IDX_DB021E96A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, read_at DATETIME DEFAULT NULL, priority VARCHAR(20) NOT NULL, icon VARCHAR(50) DEFAULT NULL, color VARCHAR(20) DEFAULT NULL, metadata JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', related_entity VARCHAR(100) DEFAULT NULL, related_entity_id INT DEFAULT NULL, INDEX IDX_6000B0D3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alerts ADD CONSTRAINT FK_F77AC06BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE assembly_votes ADD CONSTRAINT FK_77495FB2A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE assembly_votes ADD CONSTRAINT FK_77495FB2CA2E7D4C FOREIGN KEY (assembly_id) REFERENCES assemblies (id)');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B07288A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alerts DROP FOREIGN KEY FK_F77AC06BA76ED395');
        $this->addSql('ALTER TABLE assembly_votes DROP FOREIGN KEY FK_77495FB2A76ED395');
        $this->addSql('ALTER TABLE assembly_votes DROP FOREIGN KEY FK_77495FB2CA2E7D4C');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B07288A76ED395');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96A76ED395');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3A76ED395');
        $this->addSql('DROP TABLE alerts');
        $this->addSql('DROP TABLE assemblies');
        $this->addSql('DROP TABLE assembly_votes');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE notifications');
    }
}
