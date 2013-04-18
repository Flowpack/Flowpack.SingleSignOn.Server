<?php
namespace Flowpack\SingleSignOn\Server\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Interface for a mapper service that maps the globally authenticated account
 * to account data for an instance.
 */
interface ClientAccountMapperInterface {

	/**
	 * Map the given account as account data for an instance
	 *
	 * @param \Flowpack\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient
	 * @param \TYPO3\Flow\Security\Account $account
	 * @return array
	 */
	public function getAccountData(\Flowpack\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient, \TYPO3\Flow\Security\Account $account);

}
?>