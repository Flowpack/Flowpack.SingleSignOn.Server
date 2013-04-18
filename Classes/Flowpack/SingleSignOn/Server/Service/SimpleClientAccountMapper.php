<?php
namespace Flowpack\SingleSignOn\Server\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server".*
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A simple instance account mapper that will map all (safe) properties of
 * the authenticated account and the associated party.
 *
 * @Flow\Scope("singleton")
 */
class SimpleClientAccountMapper implements ClientAccountMapperInterface {

	/**
	 * @var array
	 */
	protected $configuration = NULL;

	/**
	 * Map the given account as account data for an instance
	 *
	 * @param \Flowpack\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient
	 * @param \TYPO3\Flow\Security\Account $account
	 * @return array
	 */
	public function getAccountData(\Flowpack\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient, \TYPO3\Flow\Security\Account $account) {
		if ($this->configuration !== NULL) {
			$configuration = $this->configuration;
		} else {
			$configuration = $this->getDefaultConfiguration($ssoClient, $account);
		}
		$partyData = $this->transformValue($account->getParty(), $configuration['party']);
		$mappedRoles = array();
		foreach ($account->getRoles() as $role) {
			$mappedRoles[] = $role->getIdentifier();
		}
		return array(
			'accountIdentifier' => $account->getAccountIdentifier(),
			'roles' => $mappedRoles,
			'party' => $partyData
		);
	}

	/**
	 * Transforms a value depending on type recursively using the
	 * supplied configuration.
	 *
	 * @param mixed $value The value to transform
	 * @param mixed $configuration Configuration for transforming the value or NULL
	 * @return array The transformed value
	 */
	protected function transformValue($value, $configuration) {
		if (is_array($value) || $value instanceof \ArrayAccess) {
			$array = array();
			foreach ($value as $key => $element) {
				if (isset($configuration['_descendAll']) && is_array($configuration['_descendAll'])) {
					$array[] = $this->transformValue($element, $configuration['_descendAll']);
				} else {
					if (isset($configuration['_only']) && is_array($configuration['_only']) && !in_array($key, $configuration['_only'])) continue;
					if (isset($configuration['_exclude']) && is_array($configuration['_exclude']) && in_array($key, $configuration['_exclude'])) continue;
					$array[$key] = $this->transformValue($element, isset($configuration[$key]) ? $configuration[$key] : array());
				}
			}
			return $array;
		} elseif (is_object($value)) {
			return $this->transformObject($value, $configuration);
		} else {
			return $value;
		}
	}

	/**
	 * Traverses the given object structure in order to transform it into an
	 * array structure.
	 *
	 * @param object $object Object to traverse
	 * @param mixed $configuration Configuration for transforming the given object or NULL
	 * @return array Object structure as an aray
	 */
	protected function transformObject($object, $configuration) {
		if ($object instanceof \DateTime) {
			return $object->format('Y-m-d\TH:i:s');
		} else {
			$propertyNames = \TYPO3\Flow\Reflection\ObjectAccess::getGettablePropertyNames($object);

			$propertiesToRender = array();
			foreach ($propertyNames as $propertyName) {
				if (isset($configuration['_only']) && is_array($configuration['_only']) && !in_array($propertyName, $configuration['_only'])) continue;
				if (isset($configuration['_exclude']) && is_array($configuration['_exclude']) && in_array($propertyName, $configuration['_exclude'])) continue;

				$propertyValue = \TYPO3\Flow\Reflection\ObjectAccess::getProperty($object, $propertyName);

				if (!is_array($propertyValue) && !is_object($propertyValue)) {
					$propertiesToRender[$propertyName] = $propertyValue;
				} elseif (isset($configuration['_descend']) && array_key_exists($propertyName, $configuration['_descend'])) {
					$propertiesToRender[$propertyName] = $this->transformValue($propertyValue, $configuration['_descend'][$propertyName]);
				}
			}
			if (isset($configuration['_exposeObjectIdentifier']) && $configuration['_exposeObjectIdentifier'] === TRUE) {
				if (isset($configuration['_exposedObjectIdentifierKey']) && strlen($configuration['_exposedObjectIdentifierKey']) > 0) {
					$identityKey = $configuration['_exposedObjectIdentifierKey'];
				} else {
					$identityKey = '__identity';
				}
				$propertiesToRender[$identityKey] = $this->persistenceManager->getIdentifierByObject($object);
			}
			if (isset($configuration['_exposeType']) && $configuration['_exposeType'] === TRUE) {
				$propertiesToRender['__type'] = get_class($object);
			}
			return $propertiesToRender;
		}
	}

	/**
	 * Get a default configuration depending on the type of party
	 *
	 * @param \Flowpack\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient
	 * @param \TYPO3\Flow\Security\Account $account
	 * @return array
	 */
	protected function getDefaultConfiguration(\Flowpack\SingleSignOn\Server\Domain\Model\SsoClient $ssoClient, \TYPO3\Flow\Security\Account $account) {
		if ($account->getParty() instanceof \TYPO3\Party\Domain\Model\Person) {
			return array(
				'party' => array(
					'_exposeType' => TRUE,
					'_descend' => array('name' => array())
				)
			);
		}
		return array('party' => array(
			'_exposeType' => TRUE
		));
	}

	/**
	 * Mapping configuration for the party
	 *
	 * @param array $configuration
	 */
	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
	}

}
?>