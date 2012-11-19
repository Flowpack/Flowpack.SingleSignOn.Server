<?php
namespace TYPO3\SingleSignOn\Server\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Client".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A simple instance account mapper that will map all (safe) information of
 * the authenticated account and the associated party.
 */
class SimpleInstanceAccountMapper implements InstanceAccountMapperInterface {

	/**
	 * Map the given account as account data for an instance
	 *
	 * @param \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient
	 * @param \TYPO3\Flow\Security\Account $account
	 * @return array
	 */
	public function getAccountData(\TYPO3\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient, \TYPO3\Flow\Security\Account $account) {
		return array(
			'accountIdentifier' => $account->getAccountIdentifier(),
			'roles' => array_map(function($role) { return $role->getIdentifier(); }, $account->getRoles()),
			'party' => array(
				// TODO Map public party properties
			)
		);
	}

}
?>