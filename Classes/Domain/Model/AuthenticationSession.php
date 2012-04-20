<?php
namespace TYPO3\SingleSignOn\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.SingleSignOn".         *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Authentication session
 *
 * @FLOW3\Entity
 */
class AuthenticationSession {

	/**
	 * The last access date
	 * @var \DateTime
	 */
	protected $lastAccessDate;

	/**
	 * The authentication status
	 * @var integer
	 */
	protected $authenticationStatus;

	/**
	 * The account identifier
	 * @var string
	 */
	protected $accountIdentifier;


	/**
	 * Get the Authentication session's last access date
	 *
	 * @return \DateTime The Authentication session's last access date
	 */
	public function getLastAccessDate() {
		return $this->lastAccessDate;
	}

	/**
	 * Sets this Authentication session's last access date
	 *
	 * @param \DateTime $lastAccessDate The Authentication session's last access date
	 * @return void
	 */
	public function setLastAccessDate($lastAccessDate) {
		$this->lastAccessDate = $lastAccessDate;
	}

	/**
	 * Get the Authentication session's authentication status
	 *
	 * @return integer The Authentication session's authentication status
	 */
	public function getAuthenticationStatus() {
		return $this->authenticationStatus;
	}

	/**
	 * Sets this Authentication session's authentication status
	 *
	 * @param integer $authenticationStatus The Authentication session's authentication status
	 * @return void
	 */
	public function setAuthenticationStatus($authenticationStatus) {
		$this->authenticationStatus = $authenticationStatus;
	}

	/**
	 * Get the Authentication session's account identifier
	 *
	 * @return string The Authentication session's account identifier
	 */
	public function getAccountIdentifier() {
		return $this->accountIdentifier;
	}

	/**
	 * Sets this Authentication session's account identifier
	 *
	 * @param string $accountIdentifier The Authentication session's account identifier
	 * @return void
	 */
	public function setAccountIdentifier($accountIdentifier) {
		$this->accountIdentifier = $accountIdentifier;
	}

}
?>