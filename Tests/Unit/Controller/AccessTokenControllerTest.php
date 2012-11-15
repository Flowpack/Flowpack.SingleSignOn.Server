<?php
namespace TYPO3\SingleSignOn\Server\Tests\Unit\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

/**
 * Unit test for AccessTokenController
 */
class AccessTokenControllerTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function jsonViewSerializesAccountRoles() {
		$account = new \TYPO3\Flow\Security\Account();
		$account->setAccountIdentifier('TestAccount');
		$account->addRole(new \TYPO3\Flow\Security\Policy\Role('TestRole'));
		$person = new \TYPO3\Party\Domain\Model\Person();
		$person->setName(new \TYPO3\Party\Domain\Model\PersonName('', 'John', '', 'Doe'));
		$account->setParty($person);

		$view = $this->getAccessibleMock('TYPO3\Flow\Mvc\View\JsonView', array('dummy'));
		$view->setConfiguration(array(
			'value' => array(
				'_exclude' => array('credentialsSource', 'authenticationProviderName', 'expirationDate'),
				'_descend' => array(
					'roles' => array('_only' => 'identifier'),
					'party' => array(
						'_descend' => array(
							'name' => array()
						)
					)
				)
			)
		));
		$view->assign('value', $account);

		$output = $view->_call('renderArray');
		$this->assertEquals(array(
			'accountIdentifier' => 'TestAccount',
			'party' => array(
				'name' => array(
					'alias' => '',
					'firstName' => 'John',
					'fullName' => 'John Doe',
					'lastName' => 'Doe',
					'middleName' => '',
					'otherName' => '',
					'title' => ''
				),
				'primaryElectronicAddress' => ''
			),
			'roles' => array(
				array('identifier' => 'TestRole')
			)
		), $output);
	}
}

?>