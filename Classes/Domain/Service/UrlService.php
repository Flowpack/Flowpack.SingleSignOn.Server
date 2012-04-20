<?php
namespace TYPO3\SingleSignOn\Domain\Service;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.SingleSignOn".         *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * URL service for building single sign-on URLs
 *
 * @FLOW3\Scope("singleton")
 */
class UrlService {

	/**
	 * @var string
	 */
	protected $ssoClientIdentifier;

	/**
	 * @var string
	 */
	protected $ssoClientKeyPairUuid;

	/**
	 * @var string
	 */
	protected $ssoServerEndpointUrl;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Security\Cryptography\RsaWalletServiceInterface
	 */
	protected $rsaWalletService;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\SingleSignOn\Domain\Repository\SsoClientRepository
	 */
	protected $ssoClientRepository;

	/**
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->ssoClientIdentifier = $settings['ssoClientIdentifier'];
		$this->ssoClientKeyPairUuid = $settings['ssoClientKeyPairUuid'];
		$this->ssoServerEndpointUrl = $settings['ssoServerEndpointUrl'];
	}

	/**
	 * @param string $callbackUrl
	 * @return string
	 */
	public function buildLoginRedirectUrl($callbackUrl) {
		$url = new \TYPO3\FLOW3\Http\Uri($this->ssoServerEndpointUrl);
		$arguments = array(
			'callbackUrl' => (string)$callbackUrl,
			'ssoClientIdentifier' => $this->ssoClientIdentifier
		);
		ksort($arguments);
		$url->setQuery(http_build_query($arguments));

		$signature = $this->rsaWalletService->sign((string)$url, $this->ssoClientKeyPairUuid);
		$arguments['signature'] = $signature;
		$url->setQuery(http_build_query($arguments));

		return (string)$url;
	}

	/**
	 *
	 */
	public function buildCallbackRedirectUrl() {

	}

	/**
	 * @param \TYPO3\FLOW3\Http\Uri $uri
	 * @param string $argumentName
	 * @param string $signature
	 * @param string $ssoClientIdentifier
	 * @return boolean
	 */
	public function verifyLoginUrl($uri, $argumentName, $signature, $ssoClientIdentifier) {
		$uri = clone $uri;
		$arguments = $uri->getArguments();
		unset($arguments['signature']);
		$uri->setQuery(http_build_query($arguments));
		$originalUri = (string)$uri;

		$ssoClient = $this->ssoClientRepository->findByIdentifier($ssoClientIdentifier);
		if ($ssoClient === NULL) {
			throw new \TYPO3\FLOW3\Exception('Could not find client with identifier "' . $ssoClientIdentifier . '"', 1334940432);
		}
		return $this->rsaWalletService->verifySignature($originalUri, $signature, $ssoClient->getPublicKey());
	}

}
?>