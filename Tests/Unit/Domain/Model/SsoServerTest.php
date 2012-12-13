<?php
namespace TYPO3\SingleSignOn\Server\Tests\Unit\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Http\Uri;
use \Mockery as m;

/**
 * Unit test for SsoServer
 */
class SsoServerTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function verifyAuthenticationRequestRemovesSignatureAndCsrfTokenFromUri() {
		$endpointUri = new Uri('http://ssoserver/sso/authentication?foo=bar&callbackUri=abc&clientIdentifier=client-01&signature=xyz&__csrfToken=123');
		$request = \TYPO3\Flow\Http\Request::create($endpointUri);

		$ssoServer = new \TYPO3\SingleSignOn\Server\Domain\Model\SsoServer();

		$rsaWalletService = m::mock('TYPO3\Flow\Security\Cryptography\RsaWalletServiceInterface');
		$this->inject($ssoServer, 'rsaWalletService', $rsaWalletService);

		$ssoClient = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoClient', array(
			'getPublicKey' => 'client-public-key-fingerprint'
		));

		$rsaWalletService
			->shouldReceive('verifySignature')
			->with('http://ssoserver/sso/authentication?foo=bar&callbackUri=abc&clientIdentifier=client-01', 'xyz', 'client-public-key-fingerprint')
			->once()
			->andReturn(TRUE);

		$result = $ssoServer->verifyAuthenticationRequest($ssoClient, $request, 'signature', base64_encode('xyz'));

		$this->assertEquals(TRUE, $result);
	}

	/**
	 * @test
	 */
	public function buildCallbackRedirectUriAddsEncryptedAndSignedAccessTokenToQuery() {
		$callbackUri = 'http://ssoclient/secured';

		$ssoServer = new \TYPO3\SingleSignOn\Server\Domain\Model\SsoServer();

		$rsaWalletService = m::mock('TYPO3\Flow\Security\Cryptography\RsaWalletServiceInterface');
		$this->inject($ssoServer, 'rsaWalletService', $rsaWalletService);

		$ssoClient = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoClient', array(
			'getPublicKey' => 'client-public-key-fingerprint',
			'getServiceBaseUri' => 'http://ssoclient/sso/'
		));
		$accessToken = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\AccessToken', array(
			'getIdentifier' => 'test-access-token'
		));
		$this->inject($ssoServer, 'keyPairUuid', 'server-public-key-fingerprint');

		$rsaWalletService
			->shouldReceive('encryptWithPublicKey')
			->with('test-access-token', 'client-public-key-fingerprint')
			->once()
			->andReturn('access-token-cipher');

		$rsaWalletService
			->shouldReceive('sign')
			->with('access-token-cipher', 'server-public-key-fingerprint')
			->once()
			->andReturn('access-token-signature');

		$redirectUri = $ssoServer->buildCallbackRedirectUri($ssoClient, $accessToken, $callbackUri);
		$this->assertContains('&__typo3[singlesignon][accessToken]=YWNjZXNzLXRva2VuLWNpcGhlcg%3D%3D&__typo3[singlesignon][signature]=YWNjZXNzLXRva2VuLXNpZ25hdHVyZQ%3D%3D', (string)$redirectUri);
	}

	/**
	 * @test
	 */
	public function createAccessTokenSetsSessionId() {
		$ssoServer = new \TYPO3\SingleSignOn\Server\Domain\Model\SsoServer();

		$mockSession = m::mock('TYPO3\Flow\Session\FlowSession', array(
			'getId' => 'session-id'
		));
		$this->inject($ssoServer, 'session', $mockSession);

		$ssoClient = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoClient');
		$account = m::mock('TYPO3\Flow\Security\Account');
		$accessToken = $ssoServer->createAccessToken($ssoClient, $account);

		$this->assertEquals('session-id', $accessToken->getSessionId());
	}

	/**
	 * Check for Mockery expectations
	 */
	public function tearDown() {
		m::close();
	}

}
?>