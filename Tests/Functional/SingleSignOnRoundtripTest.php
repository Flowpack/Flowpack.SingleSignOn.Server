<?php
namespace TYPO3\SingleSignOn\Server\Tests\Functional;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */


use \TYPO3\Flow\Http\Request;
use \TYPO3\Flow\Http\Response;
use \TYPO3\Flow\Http\Uri;

/**
 * SSO roundtrip test
 *
 * Let's see how much we can test using functional tests.
 */
class SingleSignOnRoundtripTest extends \TYPO3\Flow\Tests\FunctionalTestCase {

	protected $testableSecurityEnabled = TRUE;

	protected $testableHttpEnabled = TRUE;

	protected static $testablePersistenceEnabled = TRUE;

	/**
	 * @var \TYPO3\Flow\Security\Cryptography\RsaWalletServiceInterface
	 */
	protected $rsaWalletService;

	/**
	 * @var \TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

	/**
	 * @var \TYPO3\SingleSignOn\Server\Domain\Model\SsoServer
	 */
	protected $serverSsoServer;

	/**
	 * @var \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient
	 */
	protected $serverSsoClient;

	/**
	 * Register fixture key pairs
	 */
	public function setUp() {
		parent::setUp();
		$this->rsaWalletService = $this->objectManager->get('TYPO3\Flow\Security\Cryptography\RsaWalletServiceInterface');

		$privateKeyString = file_get_contents(__DIR__ . '/Fixtures/ssoserver.key');
		$this->rsaWalletService->registerKeyPairFromPrivateKeyString($privateKeyString);

		$privateKeyString = file_get_contents(__DIR__ . '/Fixtures/ssoclient.key');
		$this->rsaWalletService->registerKeyPairFromPrivateKeyString($privateKeyString);

		$this->ssoClientRepository = $this->objectManager->get('TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository');

		$this->serverSsoServer = $this->objectManager->get('TYPO3\SingleSignOn\Server\Domain\Factory\SsoServerFactory')->create();
	}

	/**
	 * Step 2 - 3 in SSO process (on client)
	 *
	 * @test
	 */
	public function entryPointRedirectsToEndpointWithSsoArguments() {
		$request = Request::create(new Uri('http://ssoinstance/secured'), 'GET', array('foo' => 'bar'));
		$response = new Response();

		$entryPoint = new \TYPO3\SingleSignOn\Client\Security\EntryPoint\SingleSignOnRedirect();
		$entryPoint->setOptions(array(
			'server' => 'TestServer'
		));
		$entryPoint->startAuthentication($request, $response);

		$redirectUri = new Uri($response->getHeader('Location'));
		$this->assertEquals('ssoserver', $redirectUri->getHost());
		$this->assertEquals('/test/sso/authentication', $redirectUri->getPath());

		$arguments = $redirectUri->getArguments();
		$this->assertEquals('http://ssoinstance/secured', $arguments['callbackUri']);
		$this->assertEquals('client-01', $arguments['ssoClientIdentifier']);
		$this->assertNotEquals('', $arguments['signature']);
	}

	/**
	 * Step 4 in SSO process (on server)
	 *
	 * @test
	 * @depends entryPointRedirectsToEndpointWithSsoArguments
	 */
	public function ssoEndpointWithAuthenticatedAccountRedirectsToCallbackUri() {
		$account = new \TYPO3\Flow\Security\Account();
		$account->setAccountIdentifier('testuser');
		$account->setRoles(array('User'));
		$account->setAuthenticationProviderName('SingleSignOn');
		$this->authenticateAccount($account);

		$this->setUpServerFixtures();

		$this->persistenceManager->persistAll();

			// Create URI to SSO endpoint
		$request = Request::create(new Uri('http://ssoinstance/test/secured?foo=bar'), 'GET');
		$response = new Response();

		$entryPoint = new \TYPO3\SingleSignOn\Client\Security\EntryPoint\SingleSignOnRedirect();
		$entryPoint->setOptions(array(
			'server' => 'TestServer'
		));
		$entryPoint->startAuthentication($request, $response);

		$redirectUri = new Uri($response->getHeader('Location'));

		$route = new \TYPO3\Flow\Mvc\Routing\Route();
		$route->setName('Functional Test - SSO Endpoint');
		$route->setUriPattern('test/sso/authentication(/{@action})');
		$route->setDefaults(array(
			'@package' => 'TYPO3.SingleSignOn.Server',
			'@controller' => 'Authentication',
			'@action' => 'authenticate',
			'@format' =>'html'
		));
		$route->setAppendExceedingArguments(TRUE);
		$this->router->addRoute($route);

			// Call SSO endpoint
		$this->browser->setFollowRedirects(FALSE);
		$endpointResponse = $this->browser->request($redirectUri);
		$this->assertNotEquals(500, $endpointResponse->getStatusCode(), 'Request threw exception: ' . $endpointResponse->getContent());
		$this->assertTrue($endpointResponse->hasHeader('Location'), 'Endpoint response should redirect');
		$callbackUri = new Uri($endpointResponse->getHeader('Location'));
		$this->assertEquals('ssoinstance', $callbackUri->getHost());
		$this->assertEquals('/test/secured', $callbackUri->getPath());
		$callbackArguments = $callbackUri->getArguments();
		$this->assertTrue(isset($callbackArguments['foo']), 'Callback URI should retain previous arguments');
		$this->assertTrue(isset($callbackArguments['__typo3']['singlesignon']['accessToken']), 'Callback URI should have "__typo3[singlesignon][accessToken]" argument for access token');
		$this->assertTrue(isset($callbackArguments['__typo3']['singlesignon']['signature']), 'Callback URI should have "__typo3[singlesignon][signature]" argument for server signature');
	}

	/**
	 * @test
	 */
	public function singleSignOnProviderAuthenticatesTokenFromCallbackRequest() {
		$this->setUpServerFixtures();

		$this->persistenceManager->persistAll();

		$accessToken = new \TYPO3\SingleSignOn\Server\Domain\Model\AccessToken();
		$accessToken->setSessionId('random-test-sessionid');
		$accessToken->setSsoClient($this->serverSsoClient);

		$callbackUri = $this->serverSsoServer->buildCallbackRedirectUri($this->serverSsoClient, $accessToken, 'http://ssoinstance/test/secured');

		$callbackRequest = Request::create($callbackUri);
		$callbackActionRequest = new \TYPO3\Flow\Mvc\ActionRequest($callbackRequest);

		$singleSignOnToken = new \TYPO3\SingleSignOn\Client\Security\SingleSignOnToken();
		$singleSignOnToken->updateCredentials($callbackActionRequest);

		$this->assertEquals(\TYPO3\SingleSignOn\Client\Security\SingleSignOnToken::AUTHENTICATION_NEEDED, $singleSignOnToken->getAuthenticationStatus(), 'Authentication status should be AUTHENTICATION_NEEDED');

		$singleSignOnProvider = new \TYPO3\SingleSignOn\Client\Security\SingleSignOnProvider('SingleSignOnProvider', array(
			'server' => 'TestServer'
		));
		$singleSignOnProvider->authenticate($singleSignOnToken);
	}

	/**
	 * Set up server fixtures
	 *
	 * Adds a SSO client to the repository.
	 */
	protected function setUpServerFixtures() {
		$this->serverSsoClient = new \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient();
		$this->serverSsoClient->setIdentifier('client-01');
		$this->serverSsoClient->setPublicKey('bb45dfda9f461c22cfdd6bbb0a252d8e');
		$this->ssoClientRepository->add($this->serverSsoClient);
	}

}
?>