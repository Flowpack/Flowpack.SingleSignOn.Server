<?php
namespace TYPO3\SingleSignOn\Server\Domain\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * URL service for building single sign-on URLs for the server
 *
 * @Flow\Scope("singleton")
 */
class UrlService {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Cryptography\RsaWalletServiceInterface
	 */
	protected $rsaWalletService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

	/**
	 *
	 */
	public function buildCallbackRedirectUrl() {

	}

	/**
	 * @param \TYPO3\Flow\Http\Uri $uri
	 * @param string $argumentName
	 * @param string $signature
	 * @param string $ssoClientIdentifier
	 * @return boolean
	 */
	public function verifyLoginUrl($uri, $argumentName, $signature, $ssoClientIdentifier) {
		$uri = clone $uri;
		$arguments = $uri->getArguments();
		unset($arguments['signature']);
		$uri->setQuery(http_build_query($arguments));
		$originalUri = (string)$uri;

		$ssoClient = $this->ssoClientRepository->findByIdentifier($ssoClientIdentifier);
		if ($ssoClient === NULL) {
			throw new \TYPO3\Flow\Exception('Could not find client with identifier "' . $ssoClientIdentifier . '"', 1334940432);
		}
		return $this->rsaWalletService->verifySignature($originalUri, $signature, $ssoClient->getPublicKey());
	}

}
?>