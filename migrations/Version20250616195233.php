<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250616195233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE planche (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) DEFAULT NULL, categorie VARCHAR(20) NOT NULL, description_contenu LONGTEXT DEFAULT NULL, image_filename VARCHAR(255) DEFAULT NULL, prix NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE prise_de_vue (id INT AUTO_INCREMENT NOT NULL, photographe_id INT NOT NULL, ecole_id INT NOT NULL, type_prise_id INT NOT NULL, theme_id INT NOT NULL, type_vente_id INT NOT NULL, date DATETIME NOT NULL, nombre_eleves INT NOT NULL, nombre_classes INT NOT NULL, prix_ecole NUMERIC(10, 2) NOT NULL, prix_parent NUMERIC(10, 2) NOT NULL, commentaire LONGTEXT DEFAULT NULL, frequence VARCHAR(255) DEFAULT NULL, base_de_donnee_utilisee VARCHAR(255) DEFAULT NULL, jour_decharge VARCHAR(255) DEFAULT NULL, endroit_installation VARCHAR(255) DEFAULT NULL, INDEX IDX_3EAEED8197DB59CB (photographe_id), INDEX IDX_3EAEED8177EF1B1E (ecole_id), INDEX IDX_3EAEED817B0B7799 (type_prise_id), INDEX IDX_3EAEED8159027487 (theme_id), INDEX IDX_3EAEED81B03830F6 (type_vente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE prise_individuel_planches (prise_de_vue_id INT NOT NULL, planche_id INT NOT NULL, INDEX IDX_EA6FAC235C08B7F7 (prise_de_vue_id), INDEX IDX_EA6FAC23DA8652A8 (planche_id), PRIMARY KEY(prise_de_vue_id, planche_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE prise_fratrie_planches (prise_de_vue_id INT NOT NULL, planche_id INT NOT NULL, INDEX IDX_61E7B3155C08B7F7 (prise_de_vue_id), INDEX IDX_61E7B315DA8652A8 (planche_id), PRIMARY KEY(prise_de_vue_id, planche_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_9775E7085E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE type_prise (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_8E06B77F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE type_vente (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_8686ADBB5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_de_vue ADD CONSTRAINT FK_3EAEED8197DB59CB FOREIGN KEY (photographe_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_de_vue ADD CONSTRAINT FK_3EAEED8177EF1B1E FOREIGN KEY (ecole_id) REFERENCES ecole (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_de_vue ADD CONSTRAINT FK_3EAEED817B0B7799 FOREIGN KEY (type_prise_id) REFERENCES type_prise (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_de_vue ADD CONSTRAINT FK_3EAEED8159027487 FOREIGN KEY (theme_id) REFERENCES theme (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_de_vue ADD CONSTRAINT FK_3EAEED81B03830F6 FOREIGN KEY (type_vente_id) REFERENCES type_vente (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_individuel_planches ADD CONSTRAINT FK_EA6FAC235C08B7F7 FOREIGN KEY (prise_de_vue_id) REFERENCES prise_de_vue (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_individuel_planches ADD CONSTRAINT FK_EA6FAC23DA8652A8 FOREIGN KEY (planche_id) REFERENCES planche (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_fratrie_planches ADD CONSTRAINT FK_61E7B3155C08B7F7 FOREIGN KEY (prise_de_vue_id) REFERENCES prise_de_vue (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_fratrie_planches ADD CONSTRAINT FK_61E7B315DA8652A8 FOREIGN KEY (planche_id) REFERENCES planche (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_de_vue DROP FOREIGN KEY FK_3EAEED8197DB59CB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_de_vue DROP FOREIGN KEY FK_3EAEED8177EF1B1E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_de_vue DROP FOREIGN KEY FK_3EAEED817B0B7799
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_de_vue DROP FOREIGN KEY FK_3EAEED8159027487
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_de_vue DROP FOREIGN KEY FK_3EAEED81B03830F6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_individuel_planches DROP FOREIGN KEY FK_EA6FAC235C08B7F7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_individuel_planches DROP FOREIGN KEY FK_EA6FAC23DA8652A8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_fratrie_planches DROP FOREIGN KEY FK_61E7B3155C08B7F7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE prise_fratrie_planches DROP FOREIGN KEY FK_61E7B315DA8652A8
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE planche
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE prise_de_vue
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE prise_individuel_planches
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE prise_fratrie_planches
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE theme
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE type_prise
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE type_vente
        SQL);
    }
}
