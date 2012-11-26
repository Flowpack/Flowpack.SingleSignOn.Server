<?php
namespace TYPO3\SingleSignOn\Server\Exception;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */


/**
 * A signature verification failed exception
 */
class SignatureVerificationFailedException extends \TYPO3\SingleSignOn\Server\Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = 403;

}
?>