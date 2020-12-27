<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Contracts\Security\Enum\Permission;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201223080701 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs    
        $this->addSql('ALTER TABLE role ADD permissions INT DEFAULT NULL COMMENT \'(DC2Type:permission)\'');
        $this->addSql('UPDATE role SET permissions = '.Permission::get(Permission::USER_VIEW_LIST | Permission::USER_VIEW_SELF)->getValue().' WHERE name LIKE "ROLE_USER"');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role DROP permissions');
    }
}
