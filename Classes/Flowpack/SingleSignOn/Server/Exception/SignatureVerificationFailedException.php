<?php
namespace Flowpack\SingleSignOn\Server\Exception;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

/**
 * A signature verification failed exception
 */
class SignatureVerificationFailedException extends \Flowpack\SingleSignOn\Server\Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = 403;

}
?>