<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121203170856 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
			// this up() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("ALTER TABLE typo3_singlesignon_server_domain_model_accesstoken DROP FOREIGN KEY FK_BC326C58680E437");
		$this->addSql("ALTER TABLE typo3_singlesignon_server_domain_model_accesstoken ADD CONSTRAINT FK_BC326C58680E437 FOREIGN KEY (ssoclient) REFERENCES typo3_singlesignon_server_domain_model_ssoclient (baseuri) ON DELETE CASCADE");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
			// this down() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("ALTER TABLE typo3_singlesignon_server_domain_model_accesstoken DROP FOREIGN KEY FK_BC326C58680E437");
		$this->addSql("ALTER TABLE typo3_singlesignon_server_domain_model_accesstoken ADD CONSTRAINT FK_BC326C58680E437 FOREIGN KEY (ssoclient) REFERENCES typo3_singlesignon_server_domain_model_ssoclient (baseuri)");
	}
}

?>