<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190412200145 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE manager (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', department_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', manager_user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_FA2425B9AE80F5DF (department_id), UNIQUE INDEX UNIQ_FA2425B919A3E683 (manager_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manager_user (manager_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_8B9A7430783E3463 (manager_id), INDEX IDX_8B9A7430A76ED395 (user_id), PRIMARY KEY(manager_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', department_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, birth_date DATETIME NOT NULL, start_date DATETIME NOT NULL, holiday_left INT NOT NULL, active TINYINT(1) NOT NULL, activation_token CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', reference_year DATE NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), INDEX IDX_8D93D649AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE holiday (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, status VARCHAR(1) NOT NULL, date_request DATETIME NOT NULL, INDEX IDX_DC9AB234A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE manager ADD CONSTRAINT FK_FA2425B9AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE manager ADD CONSTRAINT FK_FA2425B919A3E683 FOREIGN KEY (manager_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE manager_user ADD CONSTRAINT FK_8B9A7430783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manager_user ADD CONSTRAINT FK_8B9A7430A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_DC9AB234A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE manager_user DROP FOREIGN KEY FK_8B9A7430783E3463');
        $this->addSql('ALTER TABLE manager DROP FOREIGN KEY FK_FA2425B919A3E683');
        $this->addSql('ALTER TABLE manager_user DROP FOREIGN KEY FK_8B9A7430A76ED395');
        $this->addSql('ALTER TABLE holiday DROP FOREIGN KEY FK_DC9AB234A76ED395');
        $this->addSql('ALTER TABLE manager DROP FOREIGN KEY FK_FA2425B9AE80F5DF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AE80F5DF');
        $this->addSql('DROP TABLE manager');
        $this->addSql('DROP TABLE manager_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE holiday');
        $this->addSql('DROP TABLE department');
    }
}
