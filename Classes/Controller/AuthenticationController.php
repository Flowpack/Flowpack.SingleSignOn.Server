<?php
namespace TYPO3\SingleSignOn\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.SingleSignOn".         *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Single sign-on authentication endpoint
 *
 * @FLOW3\Scope("singleton")
 */
class AuthenticationController extends \TYPO3\FLOW3\Mvc\Controller\ActionController {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\SingleSignOn\Domain\Service\UrlService
	 */
	protected $urlService;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Security\Authentication\AuthenticationManagerInterface
	 */
	protected $authenticationManager;

	/**
	 * Index action
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
			throw new \TYPO3\FLOW3\Exception('Could not verify URI', 1334937360);
		}

			// This should set the intercepted request inside the security context
			// TODO Prevent loops
		$this->authenticationManager->authenticate();

		return 'Yeah, authenticated! Got to: ' . $callbackUrl;
	}

}
?>