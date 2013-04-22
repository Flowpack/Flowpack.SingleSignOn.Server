<?php
namespace Flowpack\SingleSignOn\Server\Domain\Factory;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

use TYPO3\Flow\Annotations as Flow;
use Flowpack\SingleSignOn\Server\Exception;

/**
 * A SSO server factory
 *
 * @Flow\Scope("singleton")
 */
class SsoServerFactory {

	/**
	 * @var string
	 */
	protected $serverServiceBaseUri;

	/**
	 * @var string
	 */
	protected $serverKeyPairFingerprint;

	/**
	 * Prepare settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		if (isset($settings['server']['serviceBaseUri'])) {
			$this->serverServiceBaseUri = $settings['server']['serviceBaseUri'];
		}
		if (isset($settings['server']['keyPairFingerprint'])) {
			$this->serverKeyPairFingerprint = $settings['server']['keyPairFingerprint'];
		}
	}

	/**
	 * Build a SSO server instance from settings
	 *
	 * @return \Flowpack\SingleSignOn\Server\Domain\Model\SsoServer
	 */
	public function create() {
		$ssoServer = new \Flowpack\SingleSignOn\Server\Domain\Model\SsoServer();
		if ((string)$this->serverServiceBaseUri === '') {
			throw new Exception('Missing Flowpack.SingleSignOn.Server.server.serviceBaseUri setting', 1354805519);
		}
		$ssoServer->setServiceBaseUri($this->serverServiceBaseUri);
		if ((string)$this->serverKeyPairFingerprint === '') {
			throw new Exception('Missing Flowpack.SingleSignOn.Server.server.keyPairFingerprint setting', 1351699834);
		}
		$ssoServer->setKeyPairFingerprint($this->serverKeyPairFingerprint);
		return $ssoServer;
	}

}
?>