<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230801125909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE color (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE color_mineral (color_id INT NOT NULL, mineral_id INT NOT NULL, INDEX IDX_4E0D24517ADA1FB5 (color_id), INDEX IDX_4E0D245121F4A72C (mineral_id), PRIMARY KEY(color_id, mineral_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, mineral_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, added_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C53D045F21F4A72C (mineral_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lustre (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lustre_mineral (lustre_id INT NOT NULL, mineral_id INT NOT NULL, INDEX IDX_25DEDC04E337D437 (lustre_id), INDEX IDX_25DEDC0421F4A72C (mineral_id), PRIMARY KEY(lustre_id, mineral_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mineral (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, formula VARCHAR(20) DEFAULT NULL, crystal_system VARCHAR(100) DEFAULT NULL, density DOUBLE PRECISION DEFAULT NULL, hardness INT DEFAULT NULL, fracture VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', streak VARCHAR(50) DEFAULT NULL, INDEX IDX_1D9BA97F12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE variety (id INT AUTO_INCREMENT NOT NULL, mineral_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, INDEX IDX_38D6911721F4A72C (mineral_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE color_mineral ADD CONSTRAINT FK_4E0D24517ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE color_mineral ADD CONSTRAINT FK_4E0D245121F4A72C FOREIGN KEY (mineral_id) REFERENCES mineral (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F21F4A72C FOREIGN KEY (mineral_id) REFERENCES mineral (id)');
        $this->addSql('ALTER TABLE lustre_mineral ADD CONSTRAINT FK_25DEDC04E337D437 FOREIGN KEY (lustre_id) REFERENCES lustre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lustre_mineral ADD CONSTRAINT FK_25DEDC0421F4A72C FOREIGN KEY (mineral_id) REFERENCES mineral (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mineral ADD CONSTRAINT FK_1D9BA97F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE variety ADD CONSTRAINT FK_38D6911721F4A72C FOREIGN KEY (mineral_id) REFERENCES mineral (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE color_mineral DROP FOREIGN KEY FK_4E0D24517ADA1FB5');
        $this->addSql('ALTER TABLE color_mineral DROP FOREIGN KEY FK_4E0D245121F4A72C');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F21F4A72C');
        $this->addSql('ALTER TABLE lustre_mineral DROP FOREIGN KEY FK_25DEDC04E337D437');
        $this->addSql('ALTER TABLE lustre_mineral DROP FOREIGN KEY FK_25DEDC0421F4A72C');
        $this->addSql('ALTER TABLE mineral DROP FOREIGN KEY FK_1D9BA97F12469DE2');
        $this->addSql('ALTER TABLE variety DROP FOREIGN KEY FK_38D6911721F4A72C');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE color_mineral');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE lustre');
        $this->addSql('DROP TABLE lustre_mineral');
        $this->addSql('DROP TABLE mineral');
        $this->addSql('DROP TABLE variety');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
