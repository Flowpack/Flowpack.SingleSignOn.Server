<?php
namespace TYPO3\SingleSignOn\Security;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.SingleSignOn".         *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * A single sign-on provider
 */
class SingleSignOnProvider extends \TYPO3\FLOW3\Security\Authentication\Provider\AbstractProvider {

	/**
	 * Returns the classnames of the tokens this provider is responsible for.
	 *
	 * @return array The classname of the token this provider is responsible for
	 */
	public function getTokenClassNames() {
		return array('TYPO3\SingleSignOn\Security\SingleSignOnToken');
	}

	/**
	 * Tries to authenticate the given token. Sets isAuthenticated to TRUE if authentication succeeded.
	 *
	 * @param \TYPO3\FLOW3\Security\Authentication\TokenInterface $authenticationToken The token to be authenticated
	 * @return void
	 */
	public function authenticate(\TYPO3\FLOW3\Security\Authentication\TokenInterface $authenticationToken) {
		// 1. Check for token with parameters
		// 1.a. If token is set
		// 1.b. If token does not exist
	}

}
?>