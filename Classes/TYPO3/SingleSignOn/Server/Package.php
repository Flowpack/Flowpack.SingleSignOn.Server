<?php
namespace TYPO3\SingleSignOn\Server;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Connect SSO specific signals
 */
class Package extends \TYPO3\Flow\Package\Package {

	/**
	 * @param \TYPO3\Flow\Core\Bootstrap $bootstrap
	 * @return void
	 */
	public function boot(\TYPO3\Flow\Core\Bootstrap $bootstrap) {
		$bootstrap->getSignalSlotDispatcher()->connect(
			'TYPO3\Flow\Security\Authentication\AuthenticationProviderManager',
			'loggedOut',
			'TYPO3\SingleSignOn\Server\Service\AccountManager',
			'destroyRegisteredClientSessions'
		);
	}
}

?>