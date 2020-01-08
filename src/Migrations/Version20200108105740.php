<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200108105740 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery_note ADD team_technician_1_user_id INT DEFAULT NULL, ADD team_technician_2_user_id INT DEFAULT NULL, ADD team_technician_3_user_id INT DEFAULT NULL, ADD team_technician_4_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE delivery_note ADD CONSTRAINT FK_1E21328EC0D8862E FOREIGN KEY (team_technician_1_user_id) REFERENCES admin_user (id)');
        $this->addSql('ALTER TABLE delivery_note ADD CONSTRAINT FK_1E21328EF955BAEB FOREIGN KEY (team_technician_2_user_id) REFERENCES admin_user (id)');
        $this->addSql('ALTER TABLE delivery_note ADD CONSTRAINT FK_1E21328EEE2EAEA8 FOREIGN KEY (team_technician_3_user_id) REFERENCES admin_user (id)');
        $this->addSql('ALTER TABLE delivery_note ADD CONSTRAINT FK_1E21328E8A4FC361 FOREIGN KEY (team_technician_4_user_id) REFERENCES admin_user (id)');
        $this->addSql('CREATE INDEX IDX_1E21328EC0D8862E ON delivery_note (team_technician_1_user_id)');
        $this->addSql('CREATE INDEX IDX_1E21328EF955BAEB ON delivery_note (team_technician_2_user_id)');
        $this->addSql('CREATE INDEX IDX_1E21328EEE2EAEA8 ON delivery_note (team_technician_3_user_id)');
        $this->addSql('CREATE INDEX IDX_1E21328E8A4FC361 ON delivery_note (team_technician_4_user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery_note DROP FOREIGN KEY FK_1E21328EC0D8862E');
        $this->addSql('ALTER TABLE delivery_note DROP FOREIGN KEY FK_1E21328EF955BAEB');
        $this->addSql('ALTER TABLE delivery_note DROP FOREIGN KEY FK_1E21328EEE2EAEA8');
        $this->addSql('ALTER TABLE delivery_note DROP FOREIGN KEY FK_1E21328E8A4FC361');
        $this->addSql('DROP INDEX IDX_1E21328EC0D8862E ON delivery_note');
        $this->addSql('DROP INDEX IDX_1E21328EF955BAEB ON delivery_note');
        $this->addSql('DROP INDEX IDX_1E21328EEE2EAEA8 ON delivery_note');
        $this->addSql('DROP INDEX IDX_1E21328E8A4FC361 ON delivery_note');
        $this->addSql('ALTER TABLE delivery_note DROP team_technician_1_user_id, DROP team_technician_2_user_id, DROP team_technician_3_user_id, DROP team_technician_4_user_id');
    }
}
