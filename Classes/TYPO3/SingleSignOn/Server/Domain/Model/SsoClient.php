<?php
namespace TYPO3\SingleSignOn\Server\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use \TYPO3\SingleSignOn\Client\Domain\Model\SsoInstanceInterface;

/**
 * SSO client
 *
 * @Flow\Entity
 */
class SsoClient implements SsoInstanceInterface {

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
	 * The private key (uuid)
	 * @var string
	 */
	protected $privateKey = '';


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

	/**
	 * Get the Sso client's private key
	 *
	 * @return string The Sso client's private key
	 */
	public function getPrivateKey() {
		return $this->privateKey;
	}

	/**
	 * Sets this Sso client's private key
	 *
	 * @param string $privateKey The Sso client's private key
	 * @return void
	 */
	public function setPrivateKey($privateKey) {
		$this->privateKey = $privateKey;
	}

}
?>