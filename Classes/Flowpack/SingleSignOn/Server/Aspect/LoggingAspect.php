<?php
namespace Flowpack\SingleSignOn\Server\Aspect;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Aop\JoinPointInterface;

/**
 * An aspect which logs SSO relevant actions
 *
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class LoggingAspect {

	/**
	 * @var \Flowpack\SingleSignOn\Server\Log\SsoLoggerInterface
	 * @Flow\Inject
	 */
	protected $ssoLogger;

	/**
	 * @Flow\AfterReturning("setting(Flowpack.SingleSignOn.Server.log.logRequestSigning) && method(Flowpack\SingleSignOn\Client\Security\RequestSigner->signRequest())")
	 * @param JoinPointInterface $joinPoint The current joinpoint
	 * @return void
	 */
	public function logRequestSigning(JoinPointInterface $joinPoint) {
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

