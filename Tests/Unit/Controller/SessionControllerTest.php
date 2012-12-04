<?php
namespace TYPO3\SingleSignOn\Server\Tests\Unit\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use \Mockery as m;

/**
 * Unit test for SessionController
 */
class SessionControllerTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function destroySessionWithExistingSessionDestroysSession() {
		$controller = new \TYPO3\SingleSignOn\Server\Controller\SessionController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'DELETE'
		));
		$this->inject($controller, 'request', $mockRequest);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());
		$mockSessionManager = m::mock('TYPO3\Flow\Session\SessionManagerInterface');
		$this->inject($controller, 'sessionManager', $mockSessionManager);
		$mockSsoServer = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoServer');
		$mockSsoServerFactory = m::mock('TYPO3\SingleSignOn\Server\Domain\Factory\SsoServerFactory', array(
			'create' => $mockSsoServer
		));
		$this->inject($controller, 'ssoServerFactory', $mockSsoServerFactory);

		$mockSession = m::mock('TYPO3\Flow\Session\Session')->shouldIgnoreMissing();
		$mockSessionManager->shouldReceive('getSession')->with('valid-session-id')->andReturn($mockSession);

		$mockSession->shouldReceive('destroy')->once();

		$controller->destroyAction('valid-session-id');
	}

	/**
	 * @test
	 */
	public function destroySessionWithExistingSessionRespondsWith200() {
		$controller = new \TYPO3\SingleSignOn\Server\Controller\SessionController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'DELETE'
		));
		$this->inject($controller, 'request', $mockRequest);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());
		$mockSessionManager = m::mock('TYPO3\Flow\Session\SessionManagerInterface');
		$this->inject($controller, 'sessionManager', $mockSessionManager);
		$mockSsoServer = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoServer');
		$mockSsoServerFactory = m::mock('TYPO3\SingleSignOn\Server\Domain\Factory\SsoServerFactory', array(
			'create' => $mockSsoServer
		));
		$this->inject($controller, 'ssoServerFactory', $mockSsoServerFactory);

		$mockSession = m::mock('TYPO3\Flow\Session\Session')->shouldIgnoreMissing();
		$mockSessionManager->shouldReceive('getSession')->with('valid-session-id')->andReturn($mockSession);

		$controller->destroyAction('valid-session-id');

		$this->assertEquals(200, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function destroySessionWithUnknownSessionRespondsWith404() {
		$controller = new \TYPO3\SingleSignOn\Server\Controller\SessionController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'DELETE'
		));
		$this->inject($controller, 'request', $mockRequest);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());
		$mockSessionManager = m::mock('TYPO3\Flow\Session\SessionManagerInterface');
		$this->inject($controller, 'sessionManager', $mockSessionManager);

		$mockSessionManager->shouldReceive('getSession')->with('invalid-session-id')->andReturn(NULL);

		$controller->destroyAction('invalid-session-id');

		$this->assertEquals(404, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function destroyActionWithInvalidMethodRespondsWith405() {
		$controller = new \TYPO3\SingleSignOn\Server\Controller\SessionController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'GET'
		));
		$this->inject($controller, 'request', $mockRequest);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());

		$controller->destroyAction('test-session-id');

		$this->assertEquals(405, $response->getStatusCode());
	}

	/**
	 * Check for Mockery expectations
	 */
	public function tearDown() {
		m::close();
	}

}
?>