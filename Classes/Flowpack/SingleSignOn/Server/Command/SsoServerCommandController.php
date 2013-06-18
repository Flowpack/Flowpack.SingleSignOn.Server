<?php
namespace Flowpack\SingleSignOn\Server\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use Flowpack\SingleSignOn\Server\Domain\Model\SsoClient;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;

/**
 * Command controller of the SSO Server
 *
 * @Flow\Scope("singleton")
 */
class SsoServerCommandController extends CommandController {

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Cryptography\RsaWalletServiceInterface
	 */
	protected $rsaWalletService;

	/**
	 * Add a client
	 *
	 * This command registers the specified client at the SSO server.
	 *
	 * @param string $baseUri The client base URI as the client identifier
	 * @param string $publicKey The public key fingerprint (has to be imported using the RSA wallet service first)
	 * @return void
	 */
	public function registerClientCommand($baseUri, $publicKey) {
		try {
			$this->rsaWalletService->getPublicKey($publicKey);
		} catch(\TYPO3\Flow\Security\Exception\InvalidKeyPairIdException $exception) {
			$this->outputLine('Invalid or unknown public key fingerprint: ' . $publicKey . '. Make sure to import the key before adding the client.');
		}

		$ssoClient = new SsoClient();
		$ssoClient->setServiceBaseUri($baseUri);
		$ssoClient->setPublicKey($publicKey);
		$this->ssoClientRepository->add($ssoClient);
	}

}

?>