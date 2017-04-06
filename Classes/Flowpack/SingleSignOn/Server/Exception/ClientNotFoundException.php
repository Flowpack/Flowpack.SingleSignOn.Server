<?php
namespace Flowpack\SingleSignOn\Server\Exception;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

/**
 * A client not found exception
 */
class ClientNotFoundException extends \Flowpack\SingleSignOn\Server\Exception {

	/**
	 * @var integer
	 */
	protected $statusCode = 404;

}
