<?php
namespace TYPO3\SingleSignOn\Server\Tests\Unit\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use \Mockery as m;

/**
 * Unit test for AccessTokenController
 */
class AccessTokenControllerTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function redeemActionWithInvalidMethodRespondsWith405() {
		$controller = new \TYPO3\SingleSignOn\Server\Controller\AccessTokenController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'GET'
		));
		$this->inject($controller, 'request', $mockRequest);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());

		$controller->redeemAction('invalid-accesstoken');

		$this->assertEquals(405, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function redeemActionWithInvalidAccessTokenRespondsWith404() {
		$controller = new \TYPO3\SingleSignOn\Server\Controller\AccessTokenController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'POST'
		));
		$this->inject($controller, 'request', $mockRequest);
		$mockAccessTokenRepository = m::mock('TYPO3\SingleSignOn\Server\Domain\Repository\AccessTokenRepository', array(
			'findByIdentifier' => NULL
		));
		$this->inject($controller, 'accessTokenRepository', $mockAccessTokenRepository);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());

		$controller->redeemAction('invalid-accesstoken');

		$this->assertEquals(404, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function redeemActionWithValidAccessTokenRemovesAccessToken() {
		$controller = new \TYPO3\SingleSignOn\Server\Controller\AccessTokenController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'POST'
		));
		$this->inject($controller, 'request', $mockRequest);
		$mockAccount = m::mock('TYPO3\Flow\Security\Account');
		$mockSsoClient = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoClient');
		$mockAccessToken = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\AccessToken', array(
			'getSessionId' => 'test-sessionid',
			'getAccount' => $mockAccount,
			'getSsoClient' => $mockSsoClient
		));
		$mockAccessTokenRepository = m::mock('TYPO3\SingleSignOn\Server\Domain\Repository\AccessTokenRepository', array(
			'findByIdentifier' => $mockAccessToken
		));
		$this->inject($controller, 'accessTokenRepository', $mockAccessTokenRepository);
		$mockClientAccountMapper = m::mock('TYPO3\SingleSignOn\Server\Service\ClientAccountMapperInterface', array(
			'getAccountData' => array()
		));
		$this->inject($controller, 'clientAccountMapper', $mockClientAccountMapper);
		$this->inject($controller, 'uriBuilder', m::mock('TYPO3\Flow\Mvc\Routing\UriBuilder')->shouldIgnoreMissing());
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());

		$mockAccessTokenRepository->shouldReceive('remove')->with($mockAccessToken)->once();

		$controller->redeemAction('valid-accesstoken');
	}

	/**
	 * @test
	 */
	public function redeemActionWithValidAccessTokenMapsAccountData() {
		$controller = new \TYPO3\SingleSignOn\Server\Controller\AccessTokenController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'POST'
		));
		$this->inject($controller, 'request', $mockRequest);
		$mockAccount = m::mock('TYPO3\Flow\Security\Account');
		$mockSsoClient = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoClient');
		$mockAccessToken = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\AccessToken', array(
			'getSessionId' => 'test-sessionid',
			'getAccount' => $mockAccount,
			'getSsoClient' => $mockSsoClient
		));
		$mockAccessTokenRepository = m::mock('TYPO3\SingleSignOn\Server\Domain\Repository\AccessTokenRepository', array(
			'findByIdentifier' => $mockAccessToken
		))->shouldIgnoreMissing();
		$this->inject($controller, 'accessTokenRepository', $mockAccessTokenRepository);
		$accountData = array(
			'accountIdentifier' => 'test-account',
			'roles' => array('Administrator'),
			'party' => array('name' => 'John Doe')
		);
		$mockClientAccountMapper = m::mock('TYPO3\SingleSignOn\Server\Service\ClientAccountMapperInterface', array(
			'getAccountData' => $accountData
		));
		$this->inject($controller, 'clientAccountMapper', $mockClientAccountMapper);
		$this->inject($controller, 'uriBuilder', m::mock('TYPO3\Flow\Mvc\Routing\UriBuilder')->shouldIgnoreMissing());
		$mockView = m::mock('TYPO3\Flow\Mvc\View\ViewInterface');
		$this->inject($controller, 'view', $mockView);

		$mockView->shouldReceive('assign')->with('value',
			m::subset(array('account' => $accountData))
		)->once();

		$controller->redeemAction('valid-accesstoken');
	}

	/**
	 * @test
	 */
	public function redeemActionWithInactiveSessionRespondsWith403() {
		$this->markTestIncomplete('Implement if session manager is used');
	}

	/**
	 * Check for Mockery expectations
	 */
	public function tearDown() {
		m::close();
	}

}
?>