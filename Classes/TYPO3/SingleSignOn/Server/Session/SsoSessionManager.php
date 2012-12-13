<?php
namespace TYPO3\SingleSignOn\Server\Session;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Single sign-on session manager
 *
 * @Flow\Scope("singleton")
 */
class SsoSessionManager {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

	/**
	 * @param \TYPO3\Flow\Session\SessionInterface $session
	 * @param \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient
	 * @return void
	 */
	public function registerSsoClient(\TYPO3\Flow\Session\SessionInterface $session, \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient) {
		$registeredClients = $session->getData('TYPO3_SingleSignOn_Clients');
		if (!is_array($registeredClients)) {
			$registeredClients = array();
		}
		$registeredClients[] = $ssoClient->getServiceBaseUri();
		$session->putData('TYPO3_SingleSignOn_Clients', array_unique($registeredClients));
	}

	/**
	 * @param \TYPO3\Flow\Session\SessionInterface $session
	 * @return array Array of \TYPO3\SingleSignOn\Server\Domain\Model\SsoClient
	 */
	public function getRegisteredSsoClients(\TYPO3\Flow\Session\SessionInterface $session) {
		$registeredClients = $session->getData('TYPO3_SingleSignOn_Clients');
		if (!is_array($registeredClients)) {
			$registeredClients = array();
		}
		$ssoClients = array();
		foreach ($registeredClients as $registeredClient) {
			$ssoClients[] = $this->ssoClientRepository->findByIdentifier($registeredClient);
		}
		return $ssoClients;
	}

}
?>