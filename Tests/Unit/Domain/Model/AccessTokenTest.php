<?php
namespace Flowpack\SingleSignOn\Server\Tests\Unit\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

/**
 * Unit test for AccessToken
 */
class AccessTokenTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function constructorCreatesRandomIdentifier() {
		$accessToken = new \Flowpack\SingleSignOn\Server\Domain\Model\AccessToken();
		$this->assertNotEquals('', $accessToken->getIdentifier());
	}

}
?>