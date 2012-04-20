<?php
namespace TYPO3\SingleSignOn\Command;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.SingleSignOn".         *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Client command controller for the TYPO3.SingleSignOn package
 *
 * @FLOW3\Scope("singleton")
 */
class ClientCommandController extends \TYPO3\FLOW3\Cli\CommandController {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\SingleSignOn\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

	/**
	 * Add a client
	 *
	 * @param string $identifier This argument is required
	 * @param string $publicKey The public key uuid
	 * @return void
	 */
	public function addCommand($identifier, $publicKey) {
		$ssoClient = new \TYPO3\SingleSignOn\Domain\Model\SsoClient();
		$ssoClient->setIdentifier($identifier);
		$ssoClient->setPublicKey($publicKey);
		$this->ssoClientRepository->add($ssoClient);
	}

}

?>