<?php
namespace TYPO3\SingleSignOn\Server\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

use TYPO3\Flow\Http\Uri;
use TYPO3\SingleSignOn\Server\Exception;

/**
 * SSO client
 *
 * A persisted client configuration on the SSO server.
 *
 * @Flow\Entity
 */
class SsoClient {

	/**
	 * The SSO client base URI (acts as the identifier)
	 * @ORM\Id
	 * @Flow\Identity
	 * @var string
	 */
	protected $baseUri;

	/**
	 * The public key (uuid)
	 * @var string
	 * @Flow\Validate(type="NotEmpty")
	 */
	protected $publicKey = '';

	/**
	 * The service base path
	 * @var string
	 */
	protected $serviceBasePath = '';

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Http\Client\CurlEngine
	 */
	protected $requestEngine;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Client\Security\RequestSigner
	 */
	protected $requestSigner;

	/**
	 * @param \TYPO3\SingleSignOn\Server\Domain\Model\SsoServer $ssoServer
	 * @param string $sessionId
	 * @return void
	 */
	public function destroySession(SsoServer $ssoServer, $sessionId) {
		$serviceUri = rtrim($this->baseUri, '/') . '/' . trim($this->serviceBasePath, '/') . '/session/' . urlencode($sessionId) . '/destroy';
		$request = \TYPO3\Flow\Http\Request::create(new Uri($serviceUri), 'DELETE');

		$signedRequest = $this->requestSigner->signRequest($request, $ssoServer->getKeyPairUuid(), $ssoServer->getKeyPairUuid());

		// TODO Send request asynchronously
		$response = $this->requestEngine->sendRequest($signedRequest);
		if ($response->getStatusCode() !== 200) {
			throw new Exception('Unexpected status code for destroy session when calling "' . (string)$serviceUri . '": "' . $response->getStatus() . '"', 1354132939);
		}
	}

	/**
	 * Get the Sso client's identifier
	 *
	 * @return string The Sso client's identifier
	 */
	public function getBaseUri() {
		return $this->baseUri;
	}

	/**
	 * Sets this Sso client's identifier
	 *
	 * @param string $baseUri The Sso client's identifier
	 * @return void
	 */
	public function setBaseUri($baseUri) {
		$baseUri = rtrim($baseUri, '/') . '/';
		$this->baseUri = $baseUri;
	}

	/**
	 * Get the Sso client's public key
	 *
	 * @return string The Sso client's public key
	 */
	public function getPublicKey() {
		return $this->publicKey;
	}

	/**
	 * Sets this Sso client's public key
	 *
	 * @param string $publicKey The Sso client's public key
	 * @return void
	 */
	public function setPublicKey($publicKey) {
		$this->publicKey = $publicKey;
	}

	/**
	 * @param string $serviceBasePath
	 */
	public function setServiceBasePath($serviceBasePath) {
		$this->serviceBasePath = $serviceBasePath;
	}

	/**
	 * @return string
	 */
	public function getServiceBasePath() {
		return $this->serviceBasePath;
	}

}
?>