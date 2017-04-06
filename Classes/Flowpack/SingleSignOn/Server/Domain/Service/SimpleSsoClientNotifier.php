<?php
namespace Flowpack\SingleSignOn\Server\Domain\Service;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

use Flowpack\SingleSignOn\Server\Domain\Model\SsoServer;
use TYPO3\Flow\Annotations as Flow;

/**
 * A simple notification strategy for SSO clients
 *
 * Will synchronously call the session REST service on each client.
 *
 * @Flow\Scope("singleton")
 */
class SimpleSsoClientNotifier implements SsoClientNotifierInterface {

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Log\SsoLoggerInterface
	 */
	protected $ssoLogger;

	/**
	 * Destroy SSO client sessions by iterating through all clients
	 *
	 * @param SsoServer $ssoServer
	 * @param string $sessionId
	 * @param array $ssoClients
	 * @return void
	 */
	public function destroySession(SsoServer $ssoServer, $sessionId, array $ssoClients) {
		foreach ($ssoClients as $ssoClient) {
			/** @var \Flowpack\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient */
			$ssoClient->destroySession($ssoServer, $sessionId);
			$this->ssoLogger->log('Destroyed session "' . $sessionId . '" on client ' . $ssoClient->getServiceBaseUri(), LOG_INFO);
		}
	}

}
