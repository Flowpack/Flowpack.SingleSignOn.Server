<?php
namespace TYPO3\SingleSignOn\Server\Tests\Unit\Domain\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use \TYPO3\Flow\Http\Uri;
use \Mockery as m;

/**
 * Unit test for UriService
 */
class UriServiceTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function verifyLoginUriRemovesSignatureAndCsrfTokenFromUriForVerification() {
		$this->markTestSkipped('Refactor to SsoServer');

		$endpointUri = new Uri('http://ssoserver/sso/authentication?foo=bar&callbackUri=abc&clientIdentifier=client-01&signature=xyz&__csrfToken=123');
		$clientIdentifier = 'client-01';

		$uriService = new \TYPO3\SingleSignOn\Server\Domain\Service\UriService();

		$ssoClient = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoClient', array(
			'getPublicKey' => 'client-public-key-fingerprint'
		));
		$ssoClientRepository = m::mock('TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository', array(
			'findByIdentifier' => $ssoClient
		));
		$rsaWalletService = m::mock('TYPO3\Flow\Security\Cryptography\RsaWalletServiceInterface');
		$this->inject($uriService, 'ssoClientRepository', $ssoClientRepository);
		$this->inject($uriService, 'rsaWalletService', $rsaWalletService);

		$rsaWalletService
			->shouldReceive('verifySignature')
			->with('http://ssoserver/sso/authentication?foo=bar&callbackUri=abc&clientIdentifier=client-01', m::any(), m::any())
			->andReturn(TRUE)->once();

		$uriService->verifyLoginUri($endpointUri, 'signature', base64_encode('xyz'), $clientIdentifier);
	}

	/**
	 *
	 */
	public function tearDown() {
		m::close();
	}

}
?>