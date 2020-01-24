<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200124160222 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery_note ADD windfarm_id INT DEFAULT NULL, ADD windmill_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE delivery_note ADD CONSTRAINT FK_1E21328EA9FC0822 FOREIGN KEY (windfarm_id) REFERENCES windfarm (id)');
        $this->addSql('ALTER TABLE delivery_note ADD CONSTRAINT FK_1E21328E20C49033 FOREIGN KEY (windmill_id) REFERENCES windmill (id)');
        $this->addSql('CREATE INDEX IDX_1E21328EA9FC0822 ON delivery_note (windfarm_id)');
        $this->addSql('CREATE INDEX IDX_1E21328E20C49033 ON delivery_note (windmill_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery_note DROP FOREIGN KEY FK_1E21328EA9FC0822');
        $this->addSql('ALTER TABLE delivery_note DROP FOREIGN KEY FK_1E21328E20C49033');
        $this->addSql('DROP INDEX IDX_1E21328EA9FC0822 ON delivery_note');
        $this->addSql('DROP INDEX IDX_1E21328E20C49033 ON delivery_note');
        $this->addSql('ALTER TABLE delivery_note DROP windfarm_id, DROP windmill_id');
    }
}
