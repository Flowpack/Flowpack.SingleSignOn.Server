<?php
namespace Flowpack\SingleSignOn\Server\Domain\Service;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

use Flowpack\SingleSignOn\Server\Domain\Model\SsoServer;
use TYPO3\Flow\Annotations as Flow;

/**
 * A notification strategy interface for SSO clients
 *
 * Allows for a future asynchronous implementation of client notifications.
 */
interface SsoClientNotifierInterface {

	/**
	 * Destroys local client sessions based on the given global session id
	 *
	 * @param SsoServer $ssoServer
	 * @param string $sessionId The global session id
	 * @param array $ssoClients Array of \Flowpack\SingleSignOn\Server\Domain\Model\SsoClient
	 * @return void
	 */
	public function destroySession(
		SsoServer $ssoServer, $sessionId, array $ssoClients);

}
