<?php
namespace Flowpack\SingleSignOn\Server\Tests\Unit\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server".*
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
		$controller = new \Flowpack\SingleSignOn\Server\Controller\SessionController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'DELETE'
		));
		$this->inject($controller, 'request', $mockRequest);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());
		$mockSessionManager = m::mock('TYPO3\Flow\Session\SessionManagerInterface');
		$this->inject($controller, 'sessionManager', $mockSessionManager);
		$mockSsoServer = m::mock('Flowpack\SingleSignOn\Server\Domain\Model\SsoServer');
		$mockSsoServerFactory = m::mock('Flowpack\SingleSignOn\Server\Domain\Factory\SsoServerFactory', array(
			'create' => $mockSsoServer
		));
		$this->inject($controller, 'ssoServerFactory', $mockSsoServerFactory);
		$mockSingleSignOnSessionManager = m::mock('Flowpack\SingleSignOn\Server\Service\SsoSessionManager', array(
			'getRegisteredSsoClients' => array()
		));
		$this->inject($controller, 'singleSignOnSessionManager', $mockSingleSignOnSessionManager);
		$mockSsoClientNotifier = m::mock('Flowpack\SingleSignOn\Server\Domain\Service\SsoClientNotifierInterface', array(
			'destroySession' => NULL
		));
		$this->inject($controller, 'ssoClientNotifier', $mockSsoClientNotifier);

		$mockSession = m::mock('TYPO3\Flow\Session\Session')->shouldIgnoreMissing();
		$mockSessionManager->shouldReceive('getSession')->with('valid-session-id')->andReturn($mockSession);

		$mockSession->shouldReceive('destroy')->once();

		$controller->destroyAction('valid-session-id');
	}

	/**
	 * @test
	 */
	public function destroySessionWithExistingSessionRespondsWith200() {
		$controller = new \Flowpack\SingleSignOn\Server\Controller\SessionController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'DELETE'
		));
		$this->inject($controller, 'request', $mockRequest);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());
		$mockSessionManager = m::mock('TYPO3\Flow\Session\SessionManagerInterface');
		$this->inject($controller, 'sessionManager', $mockSessionManager);
		$mockSsoServer = m::mock('Flowpack\SingleSignOn\Server\Domain\Model\SsoServer');
		$mockSsoServerFactory = m::mock('Flowpack\SingleSignOn\Server\Domain\Factory\SsoServerFactory', array(
			'create' => $mockSsoServer
		));
		$this->inject($controller, 'ssoServerFactory', $mockSsoServerFactory);
		$mockSingleSignOnSessionManager= m::mock('Flowpack\SingleSignOn\Server\Service\SsoSessionManager', array(
			'getRegisteredSsoClients' => array()
		));
		$this->inject($controller, 'singleSignOnSessionManager', $mockSingleSignOnSessionManager);
		$mockSsoClientNotifier = m::mock('Flowpack\SingleSignOn\Server\Domain\Service\SsoClientNotifierInterface', array(
			'destroySession' => NULL
		));
		$this->inject($controller, 'ssoClientNotifier', $mockSsoClientNotifier);

		$mockSession = m::mock('TYPO3\Flow\Session\Session')->shouldIgnoreMissing();
		$mockSessionManager->shouldReceive('getSession')->with('valid-session-id')->andReturn($mockSession);

		$controller->destroyAction('valid-session-id');

		$this->assertEquals(200, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function destroySessionWithUnknownSessionRespondsWith404() {
		$controller = new \Flowpack\SingleSignOn\Server\Controller\SessionController();

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
	public function destroyActionWithInvalidMethodRespondsWith405AndAllowedMethod() {
		$controller = new \Flowpack\SingleSignOn\Server\Controller\SessionController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'GET'
		));
		$this->inject($controller, 'request', $mockRequest);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());

		$controller->destroyAction('test-session-id');

		$this->assertEquals(405, $response->getStatusCode());
		$this->assertEquals('DELETE', $response->getHeader('Allow'));
	}

	/**
	 * @test
	 */
	public function touchActionWithPostAndValidSessionIdRespondsWith200() {
		$controller = new \Flowpack\SingleSignOn\Server\Controller\SessionController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'POST'
		));
		$this->inject($controller, 'request', $mockRequest);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());
		$mockSessionManager = m::mock('TYPO3\Flow\Session\SessionManagerInterface');
		$mockSession = m::mock('TYPO3\Flow\Session\SessionInterface')->shouldIgnoreMissing();
		$mockSessionManager->shouldReceive('getSession')->with('valid-session-id')->andReturn($mockSession);
		$this->inject($controller, 'sessionManager', $mockSessionManager);

		$controller->touchAction('valid-session-id');

		$this->assertEquals(200, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function touchActionWithPostAndValidSessionIdTouchesSession() {
		$controller = new \Flowpack\SingleSignOn\Server\Controller\SessionController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'POST'
		));
		$this->inject($controller, 'request', $mockRequest);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());
		$mockSessionManager = m::mock('TYPO3\Flow\Session\SessionManagerInterface');
		$mockSession = m::mock('TYPO3\Flow\Session\SessionInterface')->shouldIgnoreMissing();
		$mockSessionManager->shouldReceive('getSession')->with('valid-session-id')->andReturn($mockSession);
		$this->inject($controller, 'sessionManager', $mockSessionManager);

		$mockSession->shouldReceive('touch')->once();

		$controller->touchAction('valid-session-id');
	}

	/**
	 * @test
	 */
	public function touchActionWithPostAndInvalidSessionIdRespondsWith404AndJsonMessage() {
		$controller = new \Flowpack\SingleSignOn\Server\Controller\SessionController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'POST'
		));
		$this->inject($controller, 'request', $mockRequest);
		$mockView = m::mock('TYPO3\Flow\Mvc\View\ViewInterface');
		$this->inject($controller, 'view', $mockView);
		$mockSessionManager = m::mock('TYPO3\Flow\Session\SessionManagerInterface');
		$mockSessionManager->shouldReceive('getSession')->with('invalid-session-id')->andReturn(NULL);
		$this->inject($controller, 'sessionManager', $mockSessionManager);

		$mockView->shouldReceive('assign')->with('value', m::subset(
			array('error' => 'SessionNotFound')
		));

		$controller->touchAction('invalid-session-id');

		$this->assertEquals(404, $response->getStatusCode());
	}

	/**
	 * @test
	 */
	public function touchActionWithInvalidMethodRespondsWith405AndAllowedMethod() {
		$controller = new \Flowpack\SingleSignOn\Server\Controller\SessionController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest->getMethod' => 'GET'
		));
		$this->inject($controller, 'request', $mockRequest);
		$this->inject($controller, 'view', m::mock('TYPO3\Flow\Mvc\View\ViewInterface')->shouldIgnoreMissing());

		$controller->touchAction('test-session-id');

		$this->assertEquals(405, $response->getStatusCode());
		$this->assertEquals('POST', $response->getHeader('Allow'));
	}

	/**
	 * Check for Mockery expectations
	 */
	public function tearDown() {
		m::close();
	}

}
?>