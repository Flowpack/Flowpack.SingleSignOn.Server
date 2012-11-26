<?php
namespace TYPO3\SingleSignOn\Server\Tests\Unit\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use \Mockery as m;

/**
 * Unit test for EndpointController
 */
class EndpointControllerTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 * @expectedException \TYPO3\SingleSignOn\Server\Exception\ClientNotFoundException
	 */
	public function authenticateActionWithUnknownClientIdentifierThrowsException() {
		$controller = new \TYPO3\SingleSignOn\Server\Controller\EndpointController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockSsoClientRepository = m::mock('TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository', array(
			'findByIdentifier' => NULL
		));
		$this->inject($controller, 'ssoClientRepository', $mockSsoClientRepository);

		$controller->authenticateAction('invalid-client', 'http://test/', 'abcdefg');
	}

	/**
	 * @test
	 * @expectedException \TYPO3\SingleSignOn\Server\Exception\SignatureVerificationFailedException
	 */
	public function authenticateActionWithInvalidSignatureThrowsException() {
		$controller = new \TYPO3\SingleSignOn\Server\Controller\EndpointController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockHttpRequest = m::mock('TYPO3\Flow\Http\Request', array(
			'getUri' => 'http://test/'
		));
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest' => $mockHttpRequest
		));
		$this->inject($controller, 'request', $mockRequest);
		$mockSsoClient = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoClient');
		$mockSsoClientRepository = m::mock('TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository', array(
			'findByIdentifier' => $mockSsoClient
		));
		$this->inject($controller, 'ssoClientRepository', $mockSsoClientRepository);
		$mockSsoServer = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoServer', array(
			'verifyAuthenticationRequest' => FALSE
		));
		$mockSsoServerFactory = m::mock('TYPO3\SingleSignOn\Server\Domain\Factory\SsoServerFactory', array(
			'create' => $mockSsoServer
		));
		$this->inject($controller, 'ssoServerFactory', $mockSsoServerFactory);

		$controller->authenticateAction('invalid-client', 'http://test/', 'abcdefg');
	}

	/**
	 * @test
	 * @expectedException \TYPO3\Flow\Mvc\Exception\StopActionException
	 */
	public function authenticateActionWithValidSignatureAuthenticatesLocallyAndRedirectsBack() {
		$controller = new \TYPO3\SingleSignOn\Server\Controller\EndpointController();

		$response = new \TYPO3\Flow\Http\Response();
		$this->inject($controller, 'response', $response);
		$mockHttpRequest = m::mock('TYPO3\Flow\Http\Request', array(
			'getUri' => 'http://test/'
		));
		$mockRequest = m::mock('TYPO3\Flow\Mvc\ActionRequest', array(
			'getHttpRequest' => $mockHttpRequest
		));
		$this->inject($controller, 'request', $mockRequest);
		$mockSsoClient = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoClient');
		$mockSsoClientRepository = m::mock('TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository', array(
			'findByIdentifier' => $mockSsoClient
		));
		$this->inject($controller, 'ssoClientRepository', $mockSsoClientRepository);
		$mockSsoServer = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoServer', array(
			'verifyAuthenticationRequest' => TRUE,
			'createAccessToken' => m::mock('TYPO3\SingleSignOn\Server\Domain\Model\AccessToken')
		))->shouldIgnoreMissing();
		$mockSsoServerFactory = m::mock('TYPO3\SingleSignOn\Server\Domain\Factory\SsoServerFactory', array(
			'create' => $mockSsoServer
		));
		$this->inject($controller, 'ssoServerFactory', $mockSsoServerFactory);
		$mockAuthenticationManager = m::mock('TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface');
		$this->inject($controller, 'authenticationManager', $mockAuthenticationManager);
		$mockAccount = m::mock('TYPO3\Flow\Security\Account');
		$mockAccountManager = m::mock('TYPO3\SingleSignOn\Server\Service\AccountManager', array(
			'getClientAccount' => $mockAccount
		));
		$this->inject($controller, 'accountManager', $mockAccountManager);
		$mockAccessTokenRepository = m::mock('TYPO3\SingleSignOn\Server\Domain\Repository\AccessTokenRepository')->shouldIgnoreMissing();
		$this->inject($controller, 'accessTokenRepository', $mockAccessTokenRepository);

		$mockAuthenticationManager->shouldReceive('authenticate')->once();

		$controller->authenticateAction('invalid-client', 'http://test/', 'abcdefg');
	}

}
?>