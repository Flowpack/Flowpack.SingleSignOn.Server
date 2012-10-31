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
 * Used for persistent client configurations on the SSO server.
 *
 * @Flow\Entity
 */
class SsoClient {

	/**
	 * The identifier
	 * @ORM\Id
	 * @Flow\Identity
	 * @var string
	 */
	protected $identifier;

	/**
	 * The public key (uuid)
	 * @var string
	 */
	protected $publicKey = '';

	/**
	 * Get the Sso client's identifier
	 *
	 * @return string The Sso client's identifier
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Sets this Sso client's identifier
	 *
	 * @param string $identifier The Sso client's identifier
	 * @return void
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
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

}
?>