<?php
namespace TYPO3\SingleSignOn\Security;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.SingleSignOn".         *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * A single sign-on token
 */
class SingleSignOnToken extends \TYPO3\FLOW3\Security\Authentication\Token\AbstractToken {

	/**
	 * Updates the authentication credentials, the authentication manager needs to authenticate this token.
	 * This could be a username/password from a login controller.
	 * This method is called while initializing the security context. By returning TRUE you
	 * make sure that the authentication manager will (re-)authenticate the tokens with the current credentials.
	 * Note: You should not persist the credentials!
	 *
	 * @param \TYPO3\FLOW3\Http\Request $request The current request instance
	 * @return boolean TRUE if this token needs to be (re-)authenticated
	 */
	public function updateCredentials(\TYPO3\FLOW3\Http\Request $request) {
		// TODO Get and verify parameters from redirect callback
	}

}
?>