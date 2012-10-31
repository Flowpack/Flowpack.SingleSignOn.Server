<?php
namespace TYPO3\SingleSignOn\Server\Domain\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\SingleSignOn\Server\Exception;

/**
 * A SSO server factory
 *
 * @Flow\Scope("singleton")
 */
class SsoServerFactory {

	/**
	 * @var string
	 */
	protected $serverKeyPairUuid;

	/**
	 * Prepare settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		if (isset($settings['server']['keyPairUuid'])) {
			$this->serverKeyPairUuid = $settings['server']['keyPairUuid'];
		}
	}

	/**
	 * Build a SSO server instance from settings
	 *
	 * @return \TYPO3\SingleSignOn\Server\Domain\Model\SsoServer
	 */
	public function create() {
		$ssoServer = new \TYPO3\SingleSignOn\Server\Domain\Model\SsoServer();
		if ((string)$this->serverKeyPairUuid === '') {
			throw new Exception('Missing TYPO3.SingleSignOn.Server.server.keyPairUuid setting', 1351699834);
		}
		$ssoServer->setKeyPairUuid($this->serverKeyPairUuid);
		return $ssoServer;
	}

}
?>