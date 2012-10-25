<?php
namespace TYPO3\SingleSignOn\Server\Tests\Unit\Domain\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use \TYPO3\Flow\Http\Uri;
use \Mockery as m;

/**
 * Unit test for UrlService
 */
class UrlServiceTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function verifyLoginUrlRemovesSignatureAndCsrfTokenFromUriForVerification() {
		$endpointUri = new Uri('http://ssoserver/sso/authentication?foo=bar&callbackUrl=abc&clientIdentifier=client-01&signature=xyz&__csrfToken=123');
		$clientIdentifier = 'client-01';

		$urlService = new \TYPO3\SingleSignOn\Server\Domain\Service\UrlService();

		$ssoClient = m::mock('TYPO3\SingleSignOn\Server\Domain\Model\SsoClient', array(
			'getPublicKey' => 'client-public-key-fingerprint'
		));
		$ssoClientRepository = m::mock('TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository', array(
			'findByIdentifier' => $ssoClient
		));
		$rsaWalletService = m::mock('TYPO3\Flow\Security\Cryptography\RsaWalletServiceInterface');
		$this->inject($urlService, 'ssoClientRepository', $ssoClientRepository);
		$this->inject($urlService, 'rsaWalletService', $rsaWalletService);

		$rsaWalletService
			->shouldReceive('verifySignature')
			->with('http://ssoserver/sso/authentication?foo=bar&callbackUrl=abc&clientIdentifier=client-01', m::any(), m::any())
			->andReturn(TRUE)->once();

		$urlService->verifyLoginUrl($endpointUri, 'signature', base64_encode('xyz'), $clientIdentifier);
	}

	/**
	 *
	 */
	public function tearDown() {
		m::close();
	}

}
?>