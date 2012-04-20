<?php
namespace TYPO3\SingleSignOn\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.SingleSignOn".         *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Sso server
 *
 * @FLOW3\Entity
 */
class SsoServer implements SsoInstanceInterface {

	/**
	 * The public key
	 * @var string
	 */
	protected $publicKey;

	/**
	 * The private key
	 * @var string
	 */
	protected $privateKey;

	/**
	 * The endpoint url
	 * @var string
	 */
	protected $endpointUrl;

	/**
	 * The service base url
	 * @var string
	 */
	protected $serviceBaseUrl;


	/**
	 * Get the Sso server's public key
	 *
	 * @return string The Sso server's public key
	 */
	public function getPublicKey() {
		return $this->publicKey;
	}

	/**
	 * Sets this Sso server's public key
	 *
	 * @param string $publicKey The Sso server's public key
	 * @return void
	 */
	public function setPublicKey($publicKey) {
		$this->publicKey = $publicKey;
	}

	/**
	 * Get the Sso server's private key
	 *
	 * @return string The Sso server's private key
	 */
	public function getPrivateKey() {
		return $this->privateKey;
	}

	/**
	 * Sets this Sso server's private key
	 *
	 * @param string $privateKey The Sso server's private key
	 * @return void
	 */
	public function setPrivateKey($privateKey) {
		$this->privateKey = $privateKey;
	}

	/**
	 * Get the Sso server's endpoint url
	 *
	 * @return string The Sso server's endpoint url
	 */
	public function getEndpointUrl() {
		return $this->endpointUrl;
	}

	/**
	 * Sets this Sso server's endpoint url
	 *
	 * @param string $endpointUrl The Sso server's endpoint url
	 * @return void
	 */
	public function setEndpointUrl($endpointUrl) {
		$this->endpointUrl = $endpointUrl;
	}

	/**
	 * Get the Sso server's service base url
	 *
	 * @return string The Sso server's service base url
	 */
	public function getServiceBaseUrl() {
		return $this->serviceBaseUrl;
	}

	/**
	 * Sets this Sso server's service base url
	 *
	 * @param string $serviceBaseUrl The Sso server's service base url
	 * @return void
	 */
	public function setServiceBaseUrl($serviceBaseUrl) {
		$this->serviceBaseUrl = $serviceBaseUrl;
	}

}
?>