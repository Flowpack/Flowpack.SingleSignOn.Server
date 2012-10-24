<?php
namespace TYPO3\SingleSignOn\Server\Domain\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\SingleSignOn\Server\Domain\Model\AccessToken;

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
	 * @var string
	 */
	protected $ssoServerKeyPairUuid;

	/**
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->ssoServerKeyPairUuid = $settings['ssoServerKeyPairUuid'];
	}

	/**
	 * @param string $ssoClientIdentifier
	 * @param string $callbackUrl
	 * @return \TYPO3\Flow\Http\Uri
	 */
	public function buildCallbackRedirectUrl($ssoClientIdentifier, AccessToken $accessToken, $callbackUrl) {
		$ssoClient = $this->ssoClientRepository->findByIdentifier($ssoClientIdentifier);
		if ($ssoClient === NULL) {
			throw new \TYPO3\SingleSignOn\Server\Exception\ClientNotFoundException('Could not find client with identifier "' . $ssoClientIdentifier . '"', 1334940432);
		}

		$accessTokenCipher = $this->rsaWalletService->encryptWithPublicKey($accessToken->getIdentifier(), $ssoClient->getPublicKey());
		$signature = $this->rsaWalletService->sign($accessTokenCipher, $this->ssoServerKeyPairUuid);

		$uri = new \TYPO3\Flow\Http\Uri($callbackUrl);
		// TODO Implement adding to existing query
		$uri->setQuery('__typo3[singlesignon][accessToken]=' . urlencode(base64_encode($accessTokenCipher)) . '&__typo3[singlesignon][signature]=' . urlencode(base64_encode($signature)));
		return $uri;
	}

	/**
	 * @param \TYPO3\Flow\Http\Uri $uri
	 * @param string $argumentName
	 * @param string $signature Base64 encoded signature of the URI with arguments (excluding the signature)
	 * @param string $ssoClientIdentifier
	 * @return boolean
	 */
	public function verifyLoginUrl($uri, $argumentName, $signature, $ssoClientIdentifier) {
		$uri = clone $uri;
		$arguments = $uri->getArguments();
		unset($arguments[$argumentName]);
		if (isset($arguments['__csrfToken'])) {
			unset($arguments['__csrfToken']);
		}
		$uri->setQuery(http_build_query($arguments));
		$originalUri = (string)$uri;

		$ssoClient = $this->ssoClientRepository->findByIdentifier($ssoClientIdentifier);
		if ($ssoClient === NULL) {
			throw new \TYPO3\SingleSignOn\Server\Exception\ClientNotFoundException('Could not find client with identifier "' . $ssoClientIdentifier . '"', 1334940432);
		}
		return $this->rsaWalletService->verifySignature($originalUri, base64_decode($signature), $ssoClient->getPublicKey());
	}

}
?>