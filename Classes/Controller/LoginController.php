<?php
namespace TYPO3\SingleSignOn\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.SingleSignOn".         *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * SSO server login
 *
 * @FLOW3\Scope("singleton")
 */
class LoginController extends \TYPO3\FLOW3\Security\Authentication\Controller\AuthenticationController {

	/**
	 * Render a login form
	 */
	public function indexAction() {

	}

}
?>