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
	 * @var \TYPO3\SingleSignOn\Server\Domain\Service\UrlService
	 */
	protected $urlService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface
	 */
	protected $authenticationManager;

	/**
	 * Authenticate action
	 *
	 * - Verifies the given arguments
	 * - Authenticates the request using the local authentication provider
	 * - Redirects to the given callbackUrl with a generated access token
	 *
	 * @param string $ssoClientIdentifier
	 * @param string $callbackUrl
	 * @param string $signature
	 * @return void
	 */
	public function authenticateAction($ssoClientIdentifier, $callbackUrl, $signature) {
		$uri = $this->request->getHttpRequest()->getUri();

		$isUrlValid = $this->urlService->verifyLoginUrl($uri, 'signature', $signature, $ssoClientIdentifier);
		if (!$isUrlValid) {
			throw new \TYPO3\Flow\Exception('Could not verify URI', 1334937360);
		}

			// This should set the intercepted request inside the security context
			// TODO Prevent loops
		$this->authenticationManager->authenticate();

		return 'Yeah, authenticated! Got to: ' . $callbackUrl;
	}

}
?>