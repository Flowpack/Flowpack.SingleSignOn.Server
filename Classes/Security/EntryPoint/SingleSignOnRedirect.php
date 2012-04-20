<?php
namespace TYPO3\SingleSignOn\Security\EntryPoint;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.SingleSignOn".         *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * A single sign-on token
 */
class SingleSignOnRedirect extends \TYPO3\FLOW3\Security\Authentication\EntryPoint\AbstractEntryPoint {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\SingleSignOn\Domain\Service\UrlService
	 */
	protected $urlService;

	/**
	 * Starts the authentication. (e.g. redirect to login page or send 401 HTTP header)
	 *
	 * @param \TYPO3\FLOW3\Http\Request $request The current request
	 * @param \TYPO3\FLOW3\Http\Response $response The current response
	 * @return void
	 */
	public function startAuthentication(\TYPO3\FLOW3\Http\Request $request, \TYPO3\FLOW3\Http\Response $response) {
		$callbackUrl = $request->getUri();
		$redirectUrl = $this->urlService->buildLoginRedirectUrl($callbackUrl);
		$response->setStatus(303);
		$response->setHeader('Location', $redirectUrl);
	}

}
?>