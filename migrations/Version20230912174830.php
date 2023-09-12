<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230912174830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `check` (id INT UNSIGNED AUTO_INCREMENT NOT NULL, operator_id INT UNSIGNED DEFAULT NULL, vehicle_id INT UNSIGNED DEFAULT NULL, zone_id INT UNSIGNED DEFAULT NULL, checked_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', INDEX IDX_3C8EAC13584598A3 (operator_id), INDEX IDX_3C8EAC13545317D1 (vehicle_id), INDEX IDX_3C8EAC139F2C3FAB (zone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operator (id INT UNSIGNED AUTO_INCREMENT NOT NULL, username LONGTEXT NOT NULL, password LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_D7A6A781F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parking (id INT UNSIGNED AUTO_INCREMENT NOT NULL, vehicle_id INT UNSIGNED NOT NULL, zone_id INT UNSIGNED NOT NULL, started_at DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', cost INT DEFAULT 0 NOT NULL, INDEX IDX_B237527A545317D1 (vehicle_id), INDEX IDX_B237527A9F2C3FAB (zone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id INT UNSIGNED AUTO_INCREMENT NOT NULL, zone_id INT UNSIGNED DEFAULT NULL, vehicle_id INT UNSIGNED DEFAULT NULL, amount INT NOT NULL, issued_at DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', due_at DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', paid_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', infraction VARCHAR(10) DEFAULT NULL, status VARCHAR(6) DEFAULT \'unpaid\' NOT NULL, INDEX IDX_97A0ADA39F2C3FAB (zone_id), INDEX IDX_97A0ADA3545317D1 (vehicle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE token (id INT UNSIGNED AUTO_INCREMENT NOT NULL, operator_id INT UNSIGNED DEFAULT NULL, zone_id INT UNSIGNED DEFAULT NULL, token TINYTEXT NOT NULL, expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', INDEX IDX_5F37A13B584598A3 (operator_id), INDEX IDX_5F37A13B9F2C3FAB (zone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicle (id INT UNSIGNED AUTO_INCREMENT NOT NULL, license_plate VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_1B80E486F5AA79D0 (license_plate), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zone (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(10) NOT NULL, rate INT UNSIGNED NOT NULL, type VARCHAR(6) DEFAULT \'hour\' NOT NULL, UNIQUE INDEX nameType (name, type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `check` ADD CONSTRAINT FK_3C8EAC13584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE `check` ADD CONSTRAINT FK_3C8EAC13545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE `check` ADD CONSTRAINT FK_3C8EAC139F2C3FAB FOREIGN KEY (zone_id) REFERENCES zone (id)');
        $this->addSql('ALTER TABLE parking ADD CONSTRAINT FK_B237527A545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE parking ADD CONSTRAINT FK_B237527A9F2C3FAB FOREIGN KEY (zone_id) REFERENCES zone (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA39F2C3FAB FOREIGN KEY (zone_id) REFERENCES zone (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13B584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13B9F2C3FAB FOREIGN KEY (zone_id) REFERENCES zone (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `check` DROP FOREIGN KEY FK_3C8EAC13584598A3');
        $this->addSql('ALTER TABLE `check` DROP FOREIGN KEY FK_3C8EAC13545317D1');
        $this->addSql('ALTER TABLE `check` DROP FOREIGN KEY FK_3C8EAC139F2C3FAB');
        $this->addSql('ALTER TABLE parking DROP FOREIGN KEY FK_B237527A545317D1');
        $this->addSql('ALTER TABLE parking DROP FOREIGN KEY FK_B237527A9F2C3FAB');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA39F2C3FAB');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3545317D1');
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13B584598A3');
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13B9F2C3FAB');
        $this->addSql('DROP TABLE `check`');
        $this->addSql('DROP TABLE operator');
        $this->addSql('DROP TABLE parking');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE vehicle');
        $this->addSql('DROP TABLE zone');
    }
}
