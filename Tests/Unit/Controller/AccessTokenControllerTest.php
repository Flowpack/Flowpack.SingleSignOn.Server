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
		$view = $this->getAccessibleMock('TYPO3\Flow\Mvc\View\JsonView', array('dummy'));
		$view->assign('value',
			array(
				'account' => array(
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
				),
				'sessionId' => 'abcdefg'
			)
		);

		$output = $view->_call('renderArray');
		$this->assertEquals(array(
			'account' => array(
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
			),
			'sessionId' => 'abcdefg'
		), $output);
	}
}

?>