<?php
namespace TYPO3\SingleSignOn\Server\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\SingleSignOn\Server\Exception;

/**
 * Access token management controller
 *
 * Acts as server-to-server communication to redeem access tokens
 * into account data and the global session id.
 *
 * @Flow\Scope("singleton")
 */
class AccessTokenController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Server\Domain\Repository\AccessTokenRepository
	 */
	protected $accessTokenRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SingleSignOn\Server\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'TYPO3\Flow\Mvc\View\JsonView';

	/**
	 * @var array
	 */
	protected $supportedMediaTypes = array('application/json');

	/**
	 * Redeem an access token and return global account data for the authenticated account
	 *
	 * @param string $accessToken
	 */
	public function redeemAction($accessToken) {
		$this->view->setConfiguration(array(
			'value' => array(
				'account' => array(
					'_exclude' => array('__isInitialized__', 'credentialsSource', 'authenticationProviderName', 'expirationDate'),
					'_descend' => array(
						'roles' => array('_only' => 'identifier'),
						'party' => array(
							'_descend' => array(
								'name' => array()
							)
						)
					)
				)
			)
		));

		$accessTokenObject = $this->accessTokenRepository->findByIdentifier($accessToken);
		if (!$accessTokenObject instanceof \TYPO3\SingleSignOn\Server\Domain\Model\AccessToken) {
			$this->response->setStatus(404);
			return 'Invalid access token';
		}

		$sessionId = $accessTokenObject->getSessionId();
		$this->accessTokenRepository->remove($accessTokenObject);

		// TODO Allow more flexible way of getting the account data (map authenticated to global account)
		// TODO Is it better to get the account by session id?
		$account = $accessTokenObject->getAccount();

		$this->view->assign('value', array(
			'account' => $account,
			'sessionId' => $sessionId
		));
	}

}
?>