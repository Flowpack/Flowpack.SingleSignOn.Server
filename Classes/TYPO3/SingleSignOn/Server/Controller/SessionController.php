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
	 * @var string
	 */
	protected $defaultViewObjectName = 'TYPO3\Flow\Mvc\View\JsonView';

	/**
	 * @var array
	 */
	protected $supportedMediaTypes = array('application/json');

	/**
	 * Get authentication information from a session
	 *
	 * GET /sso/session/xyz-123
	 *
	 * @param string $sessionId The session id
	 */
	public function showAction($sessionId) {

	}

	/**
	 * Touch a session to refresh the last active timestamp
	 *
	 * POST /sso/session/xyz-123/touch
	 *
	 * @param string $sessionId The session id
	 */
	public function touchAction($sessionId) {
		// TODO Touch actual session using the SessionManager
	}

	/**
	 * DELETE /sso/session/xyz-123
	 *
	 * @param string $sessionId The session id
	 */
	public function destroyAction($sessionId) {
		// TODO Remove the session using the SessionManager
		// TODO Notify clients that are registered in the session
	}

}
?>