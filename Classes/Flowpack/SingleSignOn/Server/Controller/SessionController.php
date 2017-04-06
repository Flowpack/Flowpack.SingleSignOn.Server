<?php
namespace Flowpack\SingleSignOn\Server\Controller;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

use TYPO3\Flow\Annotations as Flow;
use Flowpack\SingleSignOn\Server\Exception;

/**
 * Session management controller
 *
 * Acts as server-to-server REST service to manage global sessions.
 *
 * @Flow\Scope("singleton")
 */
class SessionController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Session\SessionManagerInterface
	 */
	protected $sessionManager;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Domain\Factory\SsoServerFactory
	 */
	protected $ssoServerFactory;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Session\SsoSessionManager
	 */
	protected $singleSignOnSessionManager;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Domain\Service\SsoClientNotifierInterface
	 */
	protected $ssoClientNotifier;

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Log\SsoLoggerInterface
	 */
	protected $ssoLogger;

	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'TYPO3\Flow\Mvc\View\JsonView';

	/**
	 * @var array
	 */
	protected $supportedMediaTypes = array('application/json');

	/**
	 * Touch a session to refresh the last active timestamp
	 *
	 * POST /sso/session/xyz-123/touch
	 *
	 * @param string $sessionId The session id
	 */
	public function touchAction($sessionId) {
		if ($this->request->getHttpRequest()->getMethod() !== 'POST') {
			$this->response->setStatus(405);
			$this->response->setHeader('Allow', 'POST');
			return;
		}

		$session = $this->sessionManager->getSession($sessionId);
		if ($session !== NULL) {
			$session->touch();

			if ($this->ssoLogger !== NULL) {
				$this->ssoLogger->log('Touched session "' . $sessionId . '"' , LOG_DEBUG);
			}

			$this->view->assign('value', array('success' => TRUE));
		} else {
			$this->response->setStatus(404);

			$this->view->assign('value', array('error' => 'SessionNotFound'));
		}
	}

	/**
	 * DELETE /sso/session/xyz-123/destroy
	 *
	 * @param string $sessionId The session id
	 * @param string $clientIdentifier Optional client identifier that will be added to any session destroy message
	 */
	public function destroyAction($sessionId, $clientIdentifier = NULL) {
		if ($this->request->getHttpRequest()->getMethod() !== 'DELETE') {
			$this->response->setStatus(405);
			$this->response->setHeader('Allow', 'DELETE');
			return;
		}

		// TODO Move the actual logic of destroying and notification to a service
		$session = $this->sessionManager->getSession($sessionId);
		if ($session !== NULL) {
			$ssoClients = $this->singleSignOnSessionManager->getRegisteredSsoClients($session);

			$message = 'Destroyed by SSO server REST service';
			if ($clientIdentifier !== NULL) {
				$message .= ' from client "' . $clientIdentifier . '"';
			}
			$session->destroy($message);

			if ($this->ssoLogger !== NULL) {
				$this->ssoLogger->log('Session service: Destroyed session "' . $sessionId . '"' . ($clientIdentifier !== NULL ? ' from client ' . $clientIdentifier : '') , LOG_INFO);
			}

			$ssoServer = $this->ssoServerFactory->create();
			$this->ssoClientNotifier->destroySession($ssoServer, $sessionId, $ssoClients);

			$this->view->assign('value', array('success' => TRUE));
		} else {
			$this->response->setStatus(404);

			$this->view->assign('value', array('error' => 'SessionNotFound'));
		}
	}

}
