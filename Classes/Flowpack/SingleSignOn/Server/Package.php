<?php
namespace Flowpack\SingleSignOn\Server;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Core\Bootstrap;

/**
 * Connect SSO specific signals
 */
class Package extends \TYPO3\Flow\Package\Package {

	/**
	 * @param Bootstrap $bootstrap
	 * @return void
	 */
	public function boot(Bootstrap $bootstrap) {
		$bootstrap->getSignalSlotDispatcher()->connect(
			'TYPO3\Flow\Security\Authentication\AuthenticationProviderManager',
			'loggedOut',
			'Flowpack\SingleSignOn\Server\Service\AccountManager',
			'destroyRegisteredClientSessions'
		);
	}
}

