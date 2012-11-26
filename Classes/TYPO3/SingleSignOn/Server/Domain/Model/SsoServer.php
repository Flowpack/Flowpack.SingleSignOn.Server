<?php
namespace TYPO3\SingleSignOn\Server\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Http\Uri;

/**
 * SSO server
 *
 * This class is instantiated from settings using the SsoServerFactory.
 */
class SsoServer {

	/**
	 * The server key pair uuid
	 * @var string
	 */
	protected $keyPairUuid;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Cryptography\RsaWalletServiceInterface
	 */
	protected $rsaWalletService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Session\SessionInterface
	 */
	protected $session;

	/**
	 * @param string $keyPairUuid
	 */
	public function setKeyPairUuid($keyPairUuid) {
		$this->keyPairUuid = $keyPairUuid;
	}

	/**
	 * @return string
	 */
	public function getKeyPairUuid() {
		return $this->keyPairUuid;
	}

	/**
	 * Verifies the authenticity of an request to the authentication endpoint
	 *
	 * Verifies the signature with the public key from the given SSO client excluding
	 * the signature argument and a possible CSRF token.
	 *
	 * @param \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient
	 * @param \TYPO3\Flow\Http\Request $request The original authentication endpoint request
	 * @param string $argumentName Argument name of the signature argument (defaults to "signature")
	 * @param string $signature Base64 encoded signature of the URI with arguments (excluding the signature argument)
	 * @return boolean
	 */
	public function verifyAuthenticationRequest(SsoClient $ssoClient, \TYPO3\Flow\Http\Request $request, $argumentName, $signature) {
		$uri = clone $request->getUri();
		$arguments = $uri->getArguments();
		unset($arguments[$argumentName]);
		if (isset($arguments['__csrfToken'])) {
			unset($arguments['__csrfToken']);
		}
		$uri->setQuery(http_build_query($arguments));
		$originalUri = (string)$uri;

		// TODO Check if RsaWalletService has a key stored for the fingerprint, e.g. import or use public key string from client
		return $this->rsaWalletService->verifySignature($originalUri, base64_decode($signature), $ssoClient->getPublicKey());
	}

	/**
	 * Builds the callback URI to the client after authentication on the server
	 *
	 * The URI will include an encrypted access token and is signed by the server private key.
	 *
	 * @param \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient
	 * @param \TYPO3\SingleSignOn\Server\Domain\Model\AccessToken $accessToken
	 * @param string $callbackUri
	 * @return \TYPO3\Flow\Http\Uri
	 */
	public function buildCallbackRedirectUri(SsoClient $ssoClient, AccessToken $accessToken, $callbackUri) {
		$accessTokenCipher = $this->rsaWalletService->encryptWithPublicKey($accessToken->getIdentifier(), $ssoClient->getPublicKey());
		$signature = $this->rsaWalletService->sign($accessTokenCipher, $this->keyPairUuid);

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
	 * Create an access token for the given SSO client
	 *
	 * The access token allows the client to get authentication details and transfer the session id.
	 *
	 * @param \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient
	 * @param \TYPO3\Flow\Security\Account $account
	 * @return \TYPO3\SingleSignOn\Server\Domain\Model\AccessToken
	 */
	public function createAccessToken(SsoClient $ssoClient, \TYPO3\Flow\Security\Account $account) {
		$accessToken = new \TYPO3\SingleSignOn\Server\Domain\Model\AccessToken();
		$accessToken->setAccount($account);
		$accessToken->setExpiryTime(time() + 60);
		$accessToken->setSessionId($this->session->getId());
		$accessToken->setSsoClient($ssoClient);
		return $accessToken;
	}

}
?>