<?php
namespace TYPO3\SingleSignOn\Server\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\SingleSignOn\Server\Exception;

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
	 * @var \TYPO3\SingleSignOn\Server\Domain\Factory\SsoServerFactory
	 */
	protected $ssoServerFactory;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

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
	 * @param string $clientIdentifier Optional client base URI to notify the client on not found session
	 * @param string $clientSessionId Optional client session id for notification if session was not found (or expired)
	 */
	public function touchAction($sessionId, $clientIdentifier = NULL, $clientSessionId = NULL) {
		if ($this->request->getHttpRequest()->getMethod() !== 'POST') {
			$this->response->setStatus(405);
			$this->response->setHeader('Allow', 'POST');
			return;
		}

		$session = $this->sessionManager->getSession($sessionId);
		if ($session !== NULL) {
			$session->touch();

			$this->view->assign('value', array('success' => TRUE));
		} else {
			$this->response->setStatus(404);

			if ($clientIdentifier !== NULL && $clientSessionId !== NULL) {
				$ssoServer = $this->ssoServerFactory->create();
				$ssoClient = $this->ssoClientRepository->findByIdentifier($clientIdentifier);
				$ssoClient->destroySession($ssoServer, $clientSessionId);
			}

			$this->view->assign('value', array('error' => 'SessionNotFound'));
		}
	}

	/**
	 * DELETE /sso/session/xyz-123/destroy
	 *
	 * @param string $sessionId The session id
	 */
	public function destroyAction($sessionId) {
		if ($this->request->getHttpRequest()->getMethod() !== 'DELETE') {
			$this->response->setStatus(405);
			$this->response->setHeader('Allow', 'DELETE');
			return;
		}

		// TODO Move the actual logic of destroying and notification to a service
		$session = $this->sessionManager->getSession($sessionId);
		if ($session !== NULL) {
			$registeredClients = $session->getData('TYPO3_SingleSignOn_Clients');
			if (!is_array($registeredClients)) {
				$registeredClients = array();
			}

			$session->destroy('Destroyed by session REST service');

			$ssoServer = $this->ssoServerFactory->create();
			foreach ($registeredClients as $clientIdentifier => $clientSessionId) {
				$ssoClient = $this->ssoClientRepository->findByIdentifier($clientIdentifier);
				$ssoClient->destroySession($ssoServer, $clientSessionId);
			}

			$this->view->assign('value', array('success' => TRUE));
		} else {
			$this->response->setStatus(404);
		}
	}

}
?>