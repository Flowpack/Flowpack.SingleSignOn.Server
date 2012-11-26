<?php
namespace TYPO3\SingleSignOn\Server\Tests\Unit\Domain\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
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
		$accessToken = new \TYPO3\SingleSignOn\Server\Domain\Model\AccessToken();
		$this->assertNotEquals('', $accessToken->getIdentifier());
	}

}
?>