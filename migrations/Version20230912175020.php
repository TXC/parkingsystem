<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230912175020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create an operator with username: user and password: password';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO operator (`username`, `password`) VALUES (\'user\', \'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi\')');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM operator WHERE `username` = \'user\'');
    }
}
