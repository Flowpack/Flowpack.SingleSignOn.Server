<?php
namespace TYPO3\SingleSignOn\Server\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Client".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Server account manager
 *
 * The account manager gets accounts for SSO clients and the
 * authenticated account on the server. It also handles account switching to deliver
 * a different account to a client than the authenticated account.
 */
class AccountManager {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface
	 */
	protected $authenticationManager;

	/**
	 * Get the currently active account for any SSO client (for the current session)
	 *
	 * @return \TYPO3\Flow\Security\Account
	 */
	public function getClientAccount() {
		$account = $this->authenticationManager->getSecurityContext()->getAccount();
		// TODO Return impersonated account (if any)
		return $account;
	}

	/**
	 * Get the currently authenticated account on the SSO server (for the current session)
	 *
	 * @return \TYPO3\Flow\Security\Account
	 */
	public function getServerAccount() {
		$account = $this->authenticationManager->getSecurityContext()->getAccount();
		return $account;
	}

	/**
	 * Impersonate another account
	 *
	 * @param \TYPO3\Flow\Security\Account $account
	 * @return void
	 */
	public function impersonateAccount(\TYPO3\Flow\Security\Account $account) {
		// TODO Implement
		// TODO Emit signal (for client notification)
	}

}
?>