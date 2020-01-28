<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200115094048 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE work_orders_windfarms (work_order_id INT NOT NULL, windfarm_id INT NOT NULL, INDEX IDX_797C808D582AE764 (work_order_id), INDEX IDX_797C808DA9FC0822 (windfarm_id), PRIMARY KEY(work_order_id, windfarm_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE work_orders_windfarms ADD CONSTRAINT FK_797C808D582AE764 FOREIGN KEY (work_order_id) REFERENCES work_order (id)');
        $this->addSql('ALTER TABLE work_orders_windfarms ADD CONSTRAINT FK_797C808DA9FC0822 FOREIGN KEY (windfarm_id) REFERENCES windfarm (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE work_orders_windfarms');
    }
}
