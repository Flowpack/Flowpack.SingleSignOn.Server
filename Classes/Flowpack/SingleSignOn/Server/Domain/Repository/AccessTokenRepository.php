<?php
namespace Flowpack\SingleSignOn\Server\Domain\Repository;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

use Flowpack\SingleSignOn\Server\Domain\Model\AccessToken;
use TYPO3\Flow\Annotations as Flow;

/**
 * A repository for AccessTokens
 *
 * @Flow\Scope("singleton")
 */
class AccessTokenRepository {

	/**
	 * Storage cache used by access tokens
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Cache\Frontend\VariableFrontend
	 */
	protected $cache;

	/**
	 * Find an access token by identifier
	 *
	 * @param string $identifier
	 * @return AccessToken The access token or NULL if none was found
	 */
	public function findByIdentifier($identifier) {
		$accessTokenResult = $this->cache->get($identifier);
		if ($accessTokenResult instanceof AccessToken) {
			return $accessTokenResult;
		}
		return NULL;
	}

	/**
	 * Add the given access token to persistence
	 *
	 * @param AccessToken $accessToken
	 * @return void
	 */
	public function add(AccessToken $accessToken) {
		$lifetime = $accessToken->getExpiryTime() - time();
		$this->cache->set($accessToken->getIdentifier(), $accessToken, array(), $lifetime);
	}

	/**
	 * Remove the given access token from persistence
	 *
	 * @param AccessToken $accessToken
	 * @return void
	 */
	public function remove(AccessToken $accessToken) {
		$this->cache->remove($accessToken->getIdentifier());
	}

	/**
	 * Remove all access tokens from persistence
	 *
	 * @return void
	 */
	public function removeAll() {
		$this->cache->flush();
	}

}
?>