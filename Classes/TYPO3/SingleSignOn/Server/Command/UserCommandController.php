<?php
namespace TYPO3\SingleSignOn\Server\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Command controller to manage accounts
 *
 * @Flow\Scope("singleton")
 */
class UserCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\AccountFactory
	 */
	protected $accountFactory;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\AccountRepository
	 */
	protected $accountRepository;

	/**
	 * Add a user account
	 *
	 * @param string $identifier The account identifier
	 * @param string $password The password of the account
	 * @param string $roles Comma separated list of roles
	 * @return void
	 */
	public function addCommand($identifier, $password, $roles) {
		$roleIdentifiers = \TYPO3\Flow\Utility\Arrays::trimExplode(',', $roles);
		$account = $this->accountFactory->createAccountWithPassword($identifier, $password, $roleIdentifiers);
		$this->accountRepository->add($account);
		$this->outputLine('Account created');
	}

}

?>