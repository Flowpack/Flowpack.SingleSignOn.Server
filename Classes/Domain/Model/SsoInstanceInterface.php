<?php
namespace TYPO3\SingleSignOn\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.SingleSignOn".         *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Sso instance (client or server)
 */
interface SsoInstanceInterface {

	/**
	 * Get the Sso client's public key
	 *
	 * @return string The Sso client's public key
	 */
	public function getPublicKey();

	/**
	 * Get the Sso client's private key
	 *
	 * @return string The Sso client's private key
	 */
	public function getPrivateKey();

}
?>