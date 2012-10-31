<?php
namespace TYPO3\SingleSignOn\Server\Domain\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\SingleSignOn\Server\Domain\Model\AccessToken;
use TYPO3\Flow\Http\Uri;

/**
 * URI service for building single sign-on URIs for the server
 *
 * @Flow\Scope("singleton")
 */
class UriService {

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
	 *
	 *
	 * @param string $ssoClientIdentifier
	 * @param \TYPO3\SingleSignOn\Server\Domain\Model\AccessToken $accessToken
	 * @param string $callbackUri
	 * @return \TYPO3\Flow\Http\Uri
	 */
	public function buildCallbackRedirectUri($ssoClientIdentifier, AccessToken $accessToken, $callbackUri) {
		$ssoClient = $this->ssoClientRepository->findByIdentifier($ssoClientIdentifier);
		if ($ssoClient === NULL) {
			throw new \TYPO3\SingleSignOn\Server\Exception\ClientNotFoundException('Could not find client with identifier "' . $ssoClientIdentifier . '"', 1334940432);
		}

		$accessTokenCipher = $this->rsaWalletService->encryptWithPublicKey($accessToken->getIdentifier(), $ssoClient->getPublicKey());
		$signature = $this->rsaWalletService->sign($accessTokenCipher, $this->ssoServerKeyPairUuid);

		$uri = new Uri($callbackUri);
		$query = $uri->getQuery();
		if ($query !== '') {
			$query = $query . '&';
		}
		$query .= '__typo3[singlesignon][accessToken]=' . urlencode(base64_encode($accessTokenCipher)) . '&__typo3[singlesignon][signature]=' . urlencode(base64_encode($signature));
		$uri->setQuery($query);
		return $uri;
	}

	/**
	 *
	 *
	 * @param \TYPO3\Flow\Http\Uri $uri
	 * @param string $argumentName
	 * @param string $signature Base64 encoded signature of the URI with arguments (excluding the signature argument)
	 * @param string $ssoClientIdentifier
	 * @return boolean
	 */
	public function verifyLoginUri(Uri $uri, $argumentName, $signature, $ssoClientIdentifier) {
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
		// TODO Check if RsaWalletService has a key stored for the fingerprint, e.g. import or use public key string from client
		return $this->rsaWalletService->verifySignature($originalUri, base64_decode($signature), $ssoClient->getPublicKey());
	}

}
?>