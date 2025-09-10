<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250910183327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE asset (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, symbol VARCHAR(10) NOT NULL, type VARCHAR(50) NOT NULL, category VARCHAR(50) DEFAULT NULL, currency VARCHAR(3) DEFAULT NULL, current_price DOUBLE PRECISION DEFAULT NULL, last_price_update DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', exchange VARCHAR(255) DEFAULT NULL, isin VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, INDEX IDX_2AF5A5CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE savings_account (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, bank_name VARCHAR(255) DEFAULT NULL, account_number VARCHAR(50) DEFAULT NULL, current_balance DOUBLE PRECISION DEFAULT NULL, annual_rate DOUBLE PRECISION DEFAULT NULL, max_amount DOUBLE PRECISION DEFAULT NULL, opening_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', maturity_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_tax_free TINYINT(1) DEFAULT NULL, tax_rate DOUBLE PRECISION DEFAULT NULL, is_active TINYINT(1) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_EA211D3AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE withdrawal (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, asset_id INT DEFAULT NULL, savings_account_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', type VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, reason VARCHAR(255) DEFAULT NULL, tax_amount DOUBLE PRECISION DEFAULT NULL, fees DOUBLE PRECISION DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6D2D3B45A76ED395 (user_id), INDEX IDX_6D2D3B455DA1941 (asset_id), INDEX IDX_6D2D3B45FCB8D9DE (savings_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE asset ADD CONSTRAINT FK_2AF5A5CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE savings_account ADD CONSTRAINT FK_EA211D3AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE withdrawal ADD CONSTRAINT FK_6D2D3B45A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE withdrawal ADD CONSTRAINT FK_6D2D3B455DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE withdrawal ADD CONSTRAINT FK_6D2D3B45FCB8D9DE FOREIGN KEY (savings_account_id) REFERENCES savings_account (id)');
        $this->addSql('ALTER TABLE transaction ADD asset_id INT DEFAULT NULL, ADD savings_account_id INT DEFAULT NULL, ADD currency VARCHAR(3) DEFAULT NULL, ADD exchange_rate DOUBLE PRECISION DEFAULT NULL, ADD reference VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE crypto_id crypto_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D15DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1FCB8D9DE FOREIGN KEY (savings_account_id) REFERENCES savings_account (id)');
        $this->addSql('CREATE INDEX IDX_723705D15DA1941 ON transaction (asset_id)');
        $this->addSql('CREATE INDEX IDX_723705D1FCB8D9DE ON transaction (savings_account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D15DA1941');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1FCB8D9DE');
        $this->addSql('ALTER TABLE asset DROP FOREIGN KEY FK_2AF5A5CA76ED395');
        $this->addSql('ALTER TABLE savings_account DROP FOREIGN KEY FK_EA211D3AA76ED395');
        $this->addSql('ALTER TABLE withdrawal DROP FOREIGN KEY FK_6D2D3B45A76ED395');
        $this->addSql('ALTER TABLE withdrawal DROP FOREIGN KEY FK_6D2D3B455DA1941');
        $this->addSql('ALTER TABLE withdrawal DROP FOREIGN KEY FK_6D2D3B45FCB8D9DE');
        $this->addSql('DROP TABLE asset');
        $this->addSql('DROP TABLE savings_account');
        $this->addSql('DROP TABLE withdrawal');
        $this->addSql('DROP INDEX IDX_723705D15DA1941 ON transaction');
        $this->addSql('DROP INDEX IDX_723705D1FCB8D9DE ON transaction');
        $this->addSql('ALTER TABLE transaction DROP asset_id, DROP savings_account_id, DROP currency, DROP exchange_rate, DROP reference, DROP created_at, DROP updated_at, CHANGE crypto_id crypto_id INT NOT NULL');
    }
}
