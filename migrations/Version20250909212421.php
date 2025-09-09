<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250909212421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_subscriptions (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, plan VARCHAR(50) NOT NULL, stripe_subscription_id VARCHAR(100) NOT NULL, stripe_customer_id VARCHAR(100) NOT NULL, stripe_price_id VARCHAR(100) NOT NULL, current_period_start DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', current_period_end DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', canceled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', trial_ends_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_EAF92751A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_subscriptions ADD CONSTRAINT FK_EAF92751A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_subscriptions DROP FOREIGN KEY FK_EAF92751A76ED395');
        $this->addSql('DROP TABLE user_subscriptions');
    }
}
