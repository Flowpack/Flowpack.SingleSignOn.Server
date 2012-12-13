<?php
namespace TYPO3\SingleSignOn\Server\Domain\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as FLOW3;

/**
 * A notification strategy interface for SSO clients
 *
 * Allows for a future asynchronous implementation of client notifications.
 */
interface SsoClientNotifierInterface {

	/**
	 * Destroys local client sessions based on the given global session id
	 *
	 * @param \TYPO3\SingleSignOn\Server\Domain\Model\SsoServer $ssoServer
	 * @param string $sessionId The global session id
	 * @param array $ssoClients Array of \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient
	 * @return void
	 */
	public function destroySession(\TYPO3\SingleSignOn\Server\Domain\Model\SsoServer $ssoServer, $sessionId, array $ssoClients);

}
?>