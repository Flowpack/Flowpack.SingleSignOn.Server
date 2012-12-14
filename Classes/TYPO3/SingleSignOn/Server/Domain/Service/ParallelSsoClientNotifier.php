<?php
namespace TYPO3\SingleSignOn\Server\Domain\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A notification strategy for SSO clients that runs in parallel
 *
 * Will synchronously call the session REST service on each client.
 *
 * @Flow\Scope("singleton")
 */
class ParallelSsoClientNotifier implements SsoClientNotifierInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Server\Log\SsoLoggerInterface
	 */
	protected $ssoLogger;

	/**
	 * @var \TYPO3\SingleSignOn\Server\Http\MultiCurlEngine
	 */
	protected $requestEngine;

	/**
	 * Destroy SSO client sessions by iterating through all clients
	 *
	 * @param \TYPO3\SingleSignOn\Server\Domain\Model\SsoServer $ssoServer
	 * @param string $sessionId
	 * @param array $ssoClients
	 * @return void
	 */
	public function destroySession(\TYPO3\SingleSignOn\Server\Domain\Model\SsoServer $ssoServer, $sessionId, array $ssoClients) {
		$requests = array();
		foreach ($ssoClients as $ssoClient) {
			/** @var \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient */
			$request = $ssoClient->buildDestroySessionRequest($ssoServer, $sessionId);
			$this->ssoLogger->log('Destroying session "' . $sessionId . '" on client ' . $ssoClient->getServiceBaseUri(), LOG_INFO);
			$requests[] = $request;
		}
		$responses = $this->requestEngine->sendRequests($requests);

		foreach ($responses as $index => $response) {
			if ($response instanceof \TYPO3\Flow\Http\Exception) {
				$this->ssoLogger->log($response->getMessage(), LOG_WARNING);
			} elseif ($response instanceof \TYPO3\Flow\Http\Response) {
				if ($response->getStatusCode() === 404 && $response->getHeader('Content-Type') === 'application/json') {
					$data = json_decode($response->getContent(), TRUE);
						// Ignore unknown sessions, could be expired on client
					if (is_array($data) && isset($data['error']) && $data['error'] === 'SessionNotFound') {
						continue;
					}
				}

				if ($response->getStatusCode() !== 200) {
					$this->ssoLogger->log('Unexpected status code for destroy session when calling "' . (string)$requests[$index]->getUri() . '": "' . $response->getStatus() . '"', LOG_WARNING);
				}
			}
		}
	}

	/**
	 * @param \TYPO3\SingleSignOn\Server\Http\MultiCurlEngine $requestEngine
	 */
	public function setRequestEngine($requestEngine) {
		$this->requestEngine = $requestEngine;
	}

}
?>