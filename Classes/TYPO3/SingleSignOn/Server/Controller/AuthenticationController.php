<?php
namespace TYPO3\SingleSignOn\Server\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\SingleSignOn\Server\Exception;
use TYPO3\SingleSignOn\Server\Exception\ClientNotFoundException;

/**
 * Single sign-on authentication endpoint
 *
 * @Flow\Scope("singleton")
 */
class AuthenticationController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Server\Domain\Factory\SsoServerFactory
	 */
	protected $ssoServerFactory;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Server\Domain\Repository\AccessTokenRepository
	 */
	protected $accessTokenRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

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
	 * - Redirects to the given callbackUri with a generated access token
	 *
	 * @param string $ssoClientIdentifier
	 * @param string $callbackUri
	 * @param string $signature
	 * @return void
	 */
	public function authenticateAction($ssoClientIdentifier, $callbackUri, $signature) {
		$ssoServer = $this->ssoServerFactory->create();
		$ssoClient = $this->ssoClientRepository->findByIdentifier($ssoClientIdentifier);
		if ($ssoClient === NULL) {
			throw new ClientNotFoundException('Client with identifier "' . $ssoClientIdentifier . '" not found', 1334940432);
		}
		$isUriValid = $ssoServer->verifyAuthenticationRequest($ssoClient, $this->request->getHttpRequest(), 'signature', $signature);
		if (!$isUriValid) {
			throw new Exception('Could not verify authentication request URI "' . $this->request->getHttpRequest()->getUri() . '"', 1334937360);
		}

			// This should set the intercepted request inside the security context
			// TODO Prevent loops
		$this->authenticationManager->authenticate();

		$account = $this->authenticationManager->getSecurityContext()->getAccount();

		$accessToken = $ssoServer->createAccessToken($ssoClient, $account);
		$this->accessTokenRepository->add($accessToken);

		$redirectUri = $ssoServer->buildCallbackRedirectUri($ssoClient, $accessToken, $callbackUri);
		$this->redirectToUri($redirectUri);
	}

}
?>