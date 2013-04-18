<?php
namespace Flowpack\SingleSignOn\Server\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

use TYPO3\Flow\Http\Uri;
use Flowpack\SingleSignOn\Server\Exception;

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
	protected $serviceBaseUri;

	/**
	 * The public key (uuid)
	 * @var string
	 * @Flow\Validate(type="NotEmpty")
	 */
	protected $publicKey = '';

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Http\Client\CurlEngine
	 */
	protected $requestEngine;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Client\Security\RequestSigner
	 */
	protected $requestSigner;

	/**
	 * Destroy a client session with the given session id
	 *
	 * The client expects a local session id and not a global session id
	 * from the SSO server.
	 *
	 * @param \Flowpack\SingleSignOn\Server\Domain\Model\SsoServer $ssoServer
	 * @param string $sessionId
	 * @return void
	 */
	public function destroySession(SsoServer $ssoServer, $sessionId) {
		$signedRequest = $this->buildDestroySessionRequest($ssoServer, $sessionId);

		$response = $this->requestEngine->sendRequest($signedRequest);

		if ($response->getStatusCode() === 404 && $response->getHeader('Content-Type') === 'application/json') {
			$data = json_decode($response->getContent(), TRUE);
			if (is_array($data) && isset($data['error']) && $data['error'] === 'SessionNotFound') {
				return;
			}
		}

		if ($response->getStatusCode() !== 200) {
			throw new Exception('Unexpected status code for destroy session when calling "' . (string)$signedRequest->getUri() . '": "' . $response->getStatus() . '"', 1354132939);
		}
	}

	/**
	 * Builds a request for calling the destroy session webservice on this client
	 *
	 * @param \Flowpack\SingleSignOn\Server\Domain\Model\SsoServer $ssoServer
	 * @param string $sessionId
	 * @return \TYPO3\Flow\Http\Request
	 */
	public function buildDestroySessionRequest(SsoServer $ssoServer, $sessionId) {
		$serviceUri = new Uri(rtrim($this->serviceBaseUri, '/') . '/session/' . urlencode($sessionId) . '/destroy');
		$serviceUri->setQuery(http_build_query(array('serverIdentifier' => $ssoServer->getServiceBaseUri())));
		$request = \TYPO3\Flow\Http\Request::create($serviceUri, 'DELETE');
		$request->setContent('');

		return $this->requestSigner->signRequest($request, $ssoServer->getKeyPairUuid(), $ssoServer->getKeyPairUuid());
	}

	/**
	 * @param string $serviceBaseUri
	 */
	public function setServiceBaseUri($serviceBaseUri) {
		$serviceBaseUri = rtrim($serviceBaseUri, '/') . '/';
		$this->serviceBaseUri = $serviceBaseUri;
	}

	/**
	 * @return string
	 */
	public function getServiceBaseUri() {
		return $this->serviceBaseUri;
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
	 * @return string
	 */
	public function __toString() {
		return $this->serviceBaseUri;
	}
}
?>