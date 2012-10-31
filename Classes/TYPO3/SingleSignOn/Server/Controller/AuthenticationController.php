<?php
namespace TYPO3\SingleSignOn\Server\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Single sign-on authentication endpoint
 *
 * @Flow\Scope("singleton")
 */
class AuthenticationController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Server\Domain\Service\UriService
	 */
	protected $uriService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Server\Domain\Repository\AccessTokenRepository
	 */
	protected $accessTokenRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface
	 */
	protected $authenticationManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Session\SessionInterface
	 */
	protected $session;

	/**
	 * Authenticate action
	 *
	 * - Verifies the given arguments
	 * - Authenticates the request using the local authentication provider
	 * - Redirects to the given callbackUri with a generated access token
	 *
	 * @param string $ssoClientIdentifier
	 * @param string $callbackUri
	 * @param string $signature
	 * @return void
	 */
	public function authenticateAction($ssoClientIdentifier, $callbackUri, $signature) {
		$uri = $this->request->getHttpRequest()->getUri();

		$isUriValid = $this->uriService->verifyLoginUri($uri, 'signature', $signature, $ssoClientIdentifier);
		if (!$isUriValid) {
			throw new \TYPO3\Flow\Exception('Could not verify URI "' . $uri . '"', 1334937360);
		}

			// This should set the intercepted request inside the security context
			// TODO Prevent loops
		$this->authenticationManager->authenticate();

			// TODO Move this logic to a domain service
		$accessToken = new \TYPO3\SingleSignOn\Server\Domain\Model\AccessToken();
		$this->accessTokenRepository->add($accessToken);
		$accessToken->setExpiryTime(time() + 60);
		$accessToken->setSessionId($this->session->getId());

		$redirectUri = $this->uriService->buildCallbackRedirectUri($ssoClientIdentifier, $accessToken, $callbackUri);
		$this->redirectToUri($redirectUri);
	}

}
?>