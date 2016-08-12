<?php
namespace Flowpack\SingleSignOn\Server\Security\RequestPattern;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

use TYPO3\Flow\Annotations as Flow;

/**
 *
 */
class SsoClientPublicKeyResolver implements \Flowpack\SingleSignOn\Client\Security\PublicKeyResolverInterface {

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

	/**
	 * @param string $identifier The identifier for looking up the public key
	 * @return string The public key fingerprint or NULL if no public key was found for the identifier
	 */
	public function resolveFingerprintByIdentifier($identifier) {
		$count = $this->ssoClientRepository->countByPublicKey($identifier);
		if ($count >= 1) {
			return $identifier;
		}
		return NULL;
	}

}
