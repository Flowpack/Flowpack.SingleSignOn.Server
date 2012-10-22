<?php
namespace TYPO3\SingleSignOn\Server\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Login controller for local authentication
 *
 * This controller will be called if no account was authenticated
 * on the SSO server.
 *
 * @Flow\Scope("singleton")
 */
class LoginController extends \TYPO3\Flow\Security\Authentication\Controller\AbstractAuthenticationController {

	/**
	 * Render a login form
	 */
	public function indexAction() {

	}

	/**
	 * Is called if authentication was successful
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $originalRequest The request that was intercepted by the security framework, NULL if there was none
	 * @return string
	 */
	protected function onAuthenticationSuccess(\TYPO3\Flow\Mvc\ActionRequest $originalRequest = NULL) {
		if ($originalRequest !== NULL) {
			$this->redirectToRequest($originalRequest);
		}
			// TODO Check if we want to throw an exception
		$this->addFlashMessage('Could not redirect to original request', 'Error in authentication', \TYPO3\Flow\Error\Message::SEVERITY_ERROR);
		$this->redirect('index');
	}

}
?>