<?php
namespace TYPO3\SingleSignOn\Server\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

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