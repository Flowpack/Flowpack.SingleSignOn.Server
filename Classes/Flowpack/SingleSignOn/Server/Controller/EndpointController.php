<?php
namespace Flowpack\SingleSignOn\Server\Controller;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

use TYPO3\Flow\Annotations as Flow;
use Flowpack\SingleSignOn\Server\Exception;
use Flowpack\SingleSignOn\Server\Exception\ClientNotFoundException;
use Flowpack\SingleSignOn\Server\Exception\SignatureVerificationFailedException;

/**
 * Public single sign-on authentication endpoint
 *
 * Acts as the authentication endpoint to transfer a global session to a client
 * (with an access token through a server-side channel).
 *
 * This controller will start authenticate the user locally if no local session
 * exists on the server.
 *
 * @Flow\Scope("singleton")
 */
class EndpointController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Domain\Factory\SsoServerFactory
	 */
	protected $ssoServerFactory;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Domain\Repository\AccessTokenRepository
	 */
	protected $accessTokenRepository;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface
	 */
	protected $authenticationManager;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Service\AccountManager
	 */
	protected $accountManager;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Log\SsoLoggerInterface
	 */
	protected $ssoLogger;

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
	 */
	public function authenticateAction($ssoClientIdentifier, $callbackUri, $signature) {
		$ssoClient = $this->ssoClientRepository->findByIdentifier($ssoClientIdentifier);
		if ($ssoClient === NULL) {
			throw new ClientNotFoundException('Client with identifier "' . $ssoClientIdentifier . '" not found', 1334940432);
		}
		$ssoServer = $this->ssoServerFactory->create();
		$isUriValid = $ssoServer->verifyAuthenticationRequest($ssoClient, $this->request->getHttpRequest(), 'signature', $signature);
		if (!$isUriValid) {
			throw new SignatureVerificationFailedException('Could not verify authentication request URI "' . $this->request->getHttpRequest()->getUri() . '"', 1334937360);
		}

			// This should set the intercepted request inside the security context
			// TODO Prevent loops
		$this->authenticationManager->authenticate();

		if (!$this->authenticationManager->isAuthenticated()) {
			throw new Exception('Expected an authenticated token after call to authenticate()', 1371568585);
		}

		$account = $this->accountManager->getClientAccount();

		$accessToken = $ssoServer->createAccessToken($ssoClient, $account);
		$this->accessTokenRepository->add($accessToken);

		if ($this->ssoLogger !== NULL) {
			$this->ssoLogger->log('Started SSO authentication for client "' . $ssoClient->getServiceBaseUri() . ' with access token "' . $accessToken . '"' , LOG_INFO);
		}

		$redirectUri = $ssoServer->buildCallbackRedirectUri($ssoClient, $accessToken, $callbackUri);
		$this->redirectToUri($redirectUri);
	}

}
?>