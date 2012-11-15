<?php
namespace TYPO3\SingleSignOn\Server\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * SSO Access Token
 *
 * A client bound one-time key to transfer the session id in a safe manner.
 *
 * @Flow\Entity
 */
class AccessToken {

	/**
	 * The identifier of the access token
	 * @ORM\Id
	 * @Flow\Identity
	 * @var string
	 */
	protected $identifier;

	/**
	 * The expiry time
	 * @var integer
	 */
	protected $expiryTime;

	/**
	 * The session id
	 * @var string
	 */
	protected $sessionId;

	/**
	 * The SSO client that initiated the request
	 * @ORM\ManyToOne
	 * @var \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient
	 */
	protected $ssoClient;

	/**
	 * @ORM\ManyToOne
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
	 * Sets this Access token's identifier
	 *
	 * @param string $identifier The Access token's identifier
	 * @return void
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
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

}
?>