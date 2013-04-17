<?php
namespace TYPO3\SingleSignOn\Server\Aspect;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Client".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * An aspect which logs SSO relevant actions
 *
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class LoggingAspect {

	/**
	 * @var \TYPO3\SingleSignOn\Server\Log\SsoLoggerInterface
	 * @Flow\Inject
	 */
	protected $ssoLogger;

	/**
	 * @Flow\AfterReturning("setting(TYPO3.SingleSignOn.Server.log.logRequestSigning) && method(TYPO3\SingleSignOn\Client\Security\RequestSigner->signRequest())")
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint The current joinpoint
	 */
	public function logRequestSigning(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {
		$request = $joinPoint->getMethodArgument('request');
		$this->ssoLogger->log('Signing request to "' . $request->getUri() . '"' . $joinPoint, LOG_DEBUG, array(
			'identifier' => $joinPoint->getMethodArgument('identifier'),
			'keyPairFingerprint' => $joinPoint->getMethodArgument('keyPairFingerprint'),
			'signatureHeader' => base64_encode($request->getHeader('X-Request-Signature')),
			'signData' => $joinPoint->getProxy()->getSignatureContent($request),
			'content' => $request->getContent(),
		));
	}

}

?>