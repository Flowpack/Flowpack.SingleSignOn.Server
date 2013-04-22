<?php
namespace Flowpack\SingleSignOn\Server\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;

/**
 * A repository for SSO Clients
 *
 * @Flow\Scope("singleton")
 */
class SsoClientRepository extends Repository {

	/**
	 * Find an SsoClient by identifier (baseUri)
	 *
	 * This method overrides the original method to normalize the
	 * given baseUri (e.g. "http://ssoclient" is normalized to "http://ssoclient/").
	 *
	 * @param string $identifier
	 * @return \Flowpack\SingleSignOn\Server\Domain\Model\SsoClient
	 */
	public function findByIdentifier($identifier) {
		$identifier = rtrim($identifier, '/') . '/';
		return parent::findByIdentifier($identifier);
	}

}
?>