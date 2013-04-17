<?php
namespace TYPO3\SingleSignOn\Server\Tests\Unit\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use \TYPO3\Flow\Http\Request;
use \TYPO3\Flow\Http\Response;
use \TYPO3\Flow\Http\Uri;

/**
 *
 */
class SimpleClientAccountMapperTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function getAccountDataMapsAccountInformation() {
		$ssoClient = new \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient();
		$account = new \TYPO3\Flow\Security\Account();
		$account->setAccountIdentifier('jdoe');
		$account->setRoles(array(new \TYPO3\Flow\Security\Policy\Role('Administrator')));

		$mapper = new \TYPO3\SingleSignOn\Server\Service\SimpleClientAccountMapper();
		$data = $mapper->getAccountData($ssoClient, $account);

		$this->assertEquals(array(
			'accountIdentifier' => 'jdoe',
			'roles' => array('Administrator'),
			'party' => NULL
		), $data);
	}

	/**
	 * @test
	 */
	public function getAccountDataMapsPublicPartyProperties() {
		$ssoClient = new \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient();
		$account = new \TYPO3\Flow\Security\Account();
		$account->setAccountIdentifier('jdoe');
		$account->setRoles(array(new \TYPO3\Flow\Security\Policy\Role('Administrator')));

		$party = new \TYPO3\Party\Domain\Model\Person();
		$party->setName(new \TYPO3\Party\Domain\Model\PersonName('', 'John', '', 'Doe'));
		$account->setParty($party);

		$mapper = new \TYPO3\SingleSignOn\Server\Service\SimpleClientAccountMapper();
		$data = $mapper->getAccountData($ssoClient, $account);

		$this->assertArrayHasKey('party', $data);
		$this->assertArrayHasKey('name', $data['party']);
		$this->assertArrayHasKey('firstName', $data['party']['name']);
		$this->assertEquals('John', $data['party']['name']['firstName']);
	}

	/**
	 * @test
	 */
	public function getAccountDataExposesTypeIfConfigured() {
		$ssoClient = new \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient();
		$account = new \TYPO3\Flow\Security\Account();
		$account->setAccountIdentifier('jdoe');
		$account->setRoles(array(new \TYPO3\Flow\Security\Policy\Role('Administrator')));

		$party = new \TYPO3\Party\Domain\Model\Person();
		$party->setName(new \TYPO3\Party\Domain\Model\PersonName('', 'John', '', 'Doe'));
		$account->setParty($party);

		$mapper = new \TYPO3\SingleSignOn\Server\Service\SimpleClientAccountMapper();
		$mapper->setConfiguration(array(
			'party' => array('_exposeType' => TRUE)
		));
		$data = $mapper->getAccountData($ssoClient, $account);

		$this->assertArrayHasKey('party', $data);
		$this->assertArrayHasKey('__type', $data['party']);
		$this->assertEquals('TYPO3\Party\Domain\Model\Person', $data['party']['__type']);
	}

}

?>