<?php
namespace TYPO3\SingleSignOn\Server\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * SSO Access Token
 *
 * A client bound one-time key to transfer the session id in a safe manner.
 */
class AccessToken {

	/**
	 * Default validity of an access token until it's considered expired
	 */
	const DEFAULT_VALIDITY_TIME = 60;

	/**
	 * The identifier of the access token
	 * @var string
	 */
	protected $identifier;

	/**
	 * The expiry time
	 * @var integer
	 */
	protected $expiryTime = 0;

	/**
	 * The session id
	 * @var string
	 */
	protected $sessionId = '';

	/**
	 * The SSO client that initiated the request
	 * @var \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient
	 */
	protected $ssoClient;

	/**
	 * @var \TYPO3\Flow\Security\Account
	 */
	protected $account;

	/**
	 * Generate an access token with a random string as identifier
	 */
	public function __construct() {
		$this->identifier = \TYPO3\Flow\Utility\Algorithms::generateRandomString(24);
	}

	/**
	 * Get the Access token's identifier
	 *
	 * @return string The Access token's identifier
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Get the Access token's expiry time
	 *
	 * @return integer The Access token's expiry time
	 */
	public function getExpiryTime() {
		return $this->expiryTime;
	}

	/**
	 * Sets this Access token's expiry time
	 *
	 * @param integer $expiryTime The Access token's expiry time
	 * @return void
	 */
	public function setExpiryTime($expiryTime) {
		$this->expiryTime = $expiryTime;
	}

	/**
	 * @param string $sessionId
	 */
	public function setSessionId($sessionId) {
		$this->sessionId = $sessionId;
	}

	/**
	 * @return string
	 */
	public function getSessionId() {
		return $this->sessionId;
	}

	/**
	 * Get the Access token's sso client
	 *
	 * @return \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient The Access token's sso client
	 */
	public function getSsoClient() {
		return $this->ssoClient;
	}

	/**
	 * Sets this Access token's sso client
	 *
	 * @param \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient The Access token's sso client
	 * @return void
	 */
	public function setSsoClient($ssoClient) {
		$this->ssoClient = $ssoClient;
	}

	/**
	 * @param \TYPO3\Flow\Security\Account $account
	 */
	public function setAccount($account) {
		$this->account = $account;
	}

	/**
	 * @return \TYPO3\Flow\Security\Account
	 */
	public function getAccount() {
		return $this->account;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->identifier;
	}

}
?>