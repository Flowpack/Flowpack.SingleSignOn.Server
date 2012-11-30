<?php
namespace TYPO3\SingleSignOn\Server\Tests\Functional\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use \TYPO3\Flow\Http\Request;
use \TYPO3\Flow\Http\Response;
use \TYPO3\Flow\Http\Uri;

/**
 * Access token controller functional test
 */
class AccessTokenControllerTest extends \TYPO3\Flow\Tests\FunctionalTestCase {

	protected $testableHttpEnabled = TRUE;

	protected static $testablePersistenceEnabled = TRUE;

	/**
	 * @var \TYPO3\SingleSignOn\Server\Domain\Model\SsoServer
	 */
	protected $serverSsoServer;

	/**
	 * @var \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient
	 */
	protected $serverSsoClient;

	/**
	 * @var \TYPO3\SingleSignOn\Server\Domain\Repository\AccessTokenRepository
	 */
	protected $accessTokenRepository;

	/**
	 * @var \TYPO3\Flow\Security\Cryptography\RsaWalletServiceInterface
	 */
	protected $rsaWalletService;

	/**
	 * Register fixture key pairs
	 */
	public function setUp() {
		parent::setUp();
		$this->serverSsoServer = $this->objectManager->get('TYPO3\SingleSignOn\Server\Domain\Factory\SsoServerFactory')->create();
		$this->accessTokenRepository = $this->objectManager->get('TYPO3\SingleSignOn\Server\Domain\Repository\AccessTokenRepository');
		$this->rsaWalletService = $this->objectManager->get('TYPO3\Flow\Security\Cryptography\RsaWalletServiceInterface');
	}

	/**
	 * @test
	 */
	public function redeemAccessTokenReturnsAuthenticationDataAsJsonAndRemovesAccessToken() {
		$this->setUpServerFixtures();

		$requestSigner = $this->objectManager->get('TYPO3\SingleSignOn\Client\Security\RequestSigner');

		$account = new \TYPO3\Flow\Security\Account();
		$account->setAccountIdentifier('testuser');
		$account->setRoles(array('User'));
		$account->setAuthenticationProviderName('SingleSignOn');
		$this->persistenceManager->add($account);

		$accessToken = new \TYPO3\SingleSignOn\Server\Domain\Model\AccessToken();
		$accessToken->setAccount($account);
		$accessToken->setSessionId('test-sessionid');
		$accessToken->setSsoClient($this->serverSsoClient);

		$this->accessTokenRepository->add($accessToken);

		$this->persistenceManager->persistAll();

		$this->setUpRoutes();

		$request = Request::create(new Uri('http://localhost/test/sso/token/' . $accessToken->getIdentifier() . '/redeem'), 'POST');
		$signedRequest = $requestSigner->signRequest($request, $this->serverSsoClient->getPublicKey(), $this->serverSsoClient->getPublicKey());
		$response = $this->browser->sendRequest($signedRequest);

		$this->assertEquals(201, $response->getStatusCode(), 'Unexpected status: ' . $response->getStatus());
		$this->assertEquals('application/json', $response->getHeader('Content-Type'), 'Unexpected Content-Type');
		$data = json_decode($response->getContent(), TRUE);
		$this->assertArrayHasKey('account', $data);
		$this->assertEquals($data['account']['accountIdentifier'], 'testuser');
		$this->assertArrayHasKey('sessionId', $data);
		$this->assertEquals($data['sessionId'], 'test-sessionid');

		$this->assertEquals(NULL, $this->accessTokenRepository->findByIdentifier($accessToken->getIdentifier()), 'Access token should be removed');
	}

	/**
	 * Set up routes
	 */
	protected function setUpRoutes() {
		$this->registerRoute('Redeem AccessToken', 'test/sso/token/{accessToken}/redeem', array(
			'@package' => 'TYPO3.SingleSignOn.Server',
			'@subpackage' => '',
			'@controller' => 'AccessToken',
			'@action' => 'redeem',
			'@format' => 'html'
		), TRUE);
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

			// Register key for request signing
		$privateKeyString = file_get_contents(__DIR__ . '/../Fixtures/ssoclient.key');
		$this->rsaWalletService->registerKeyPairFromPrivateKeyString($privateKeyString);

		$this->persistenceManager->add($this->serverSsoClient);
	}
}

?>