<?php
namespace TYPO3\SingleSignOn\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.SingleSignOn".         *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Access token
 *
 * @FLOW3\Entity
 */
class AccessToken {

	/**
	 * The identifier
	 * @var string
	 */
	protected $identifier;

	/**
	 * The expiry time
	 * @var integer
	 */
	protected $expiryTime;

	/**
	 * The authentication session
	 * @ORM\ManyToOne
	 * @var \TYPO3\SingleSignOn\Domain\Model\AuthenticationSession
	 */
	protected $authenticationSession;

	/**
	 * The sso client
	 * @ORM\ManyToOne
	 * @var \TYPO3\SingleSignOn\Domain\Model\SsoClient
	 */
	protected $ssoClient;


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
	 * Get the Access token's authentication session
	 *
	 * @return \TYPO3\SingleSignOn\Domain\Model\AuthenticationSession The Access token's authentication session
	 */
	public function getAuthenticationSession() {
		return $this->authenticationSession;
	}

	/**
	 * Sets this Access token's authentication session
	 *
	 * @param \TYPO3\SingleSignOn\Domain\Model\AuthenticationSession $authenticationSession The Access token's authentication session
	 * @return void
	 */
	public function setAuthenticationSession($authenticationSession) {
		$this->authenticationSession = $authenticationSession;
	}

	/**
	 * Get the Access token's sso client
	 *
	 * @return \TYPO3\SingleSignOn\Domain\Model\SsoClient The Access token's sso client
	 */
	public function getSsoClient() {
		return $this->ssoClient;
	}

	/**
	 * Sets this Access token's sso client
	 *
	 * @param \TYPO3\SingleSignOn\Domain\Model\SsoClient $ssoClient The Access token's sso client
	 * @return void
	 */
	public function setSsoClient($ssoClient) {
		$this->ssoClient = $ssoClient;
	}

}
?>