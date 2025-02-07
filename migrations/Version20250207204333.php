<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207204333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classe_filiere (classe_id INT NOT NULL, filiere_id INT NOT NULL, INDEX IDX_55493DFF8F5EA509 (classe_id), INDEX IDX_55493DFF180AA129 (filiere_id), PRIMARY KEY(classe_id, filiere_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classe_filiere ADD CONSTRAINT FK_55493DFF8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classe_filiere ADD CONSTRAINT FK_55493DFF180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96180AA129');
        $this->addSql('DROP INDEX IDX_8F87BF96180AA129 ON classe');
        $this->addSql('ALTER TABLE classe DROP filiere_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe_filiere DROP FOREIGN KEY FK_55493DFF8F5EA509');
        $this->addSql('ALTER TABLE classe_filiere DROP FOREIGN KEY FK_55493DFF180AA129');
        $this->addSql('DROP TABLE classe_filiere');
        $this->addSql('ALTER TABLE classe ADD filiere_id INT NOT NULL');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8F87BF96180AA129 ON classe (filiere_id)');
    }
}
