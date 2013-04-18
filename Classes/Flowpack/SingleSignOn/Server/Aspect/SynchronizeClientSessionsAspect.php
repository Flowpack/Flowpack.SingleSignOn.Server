<?php
namespace Flowpack\SingleSignOn\Server\Aspect;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Client".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 *
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class SynchronizeClientSessionsAspect {

	/**
	 * @Flow\Inject
	 * @var \Flowpack\SingleSignOn\Server\Session\SsoSessionManager
	 */
	protected $singleSignOnSessionManager;

	/**
	 *
	 *
	 * @Flow\Before("within(TYPO3\Flow\Session\SessionInterface) && method(.*->renewId())")
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The current joinpoint
	 */
	public function destroyClientSessionsOnRenewId(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		$session = $joinPoint->getProxy();
		$this->singleSignOnSessionManager->destroyRegisteredSsoClientSessions($session);
	}

}

?>