<?php
namespace Dkd\Contentdashboard;

/**
 * Extended TYPO3 Registry that provides also functionality
 * to get all entries for a namespace
 */
class Registry extends \TYPO3\CMS\Core\Registry {

	/**
	 * Get all entries for the given namespace
	 *
	 * @param $namespace
	 * @return array
	 */
	public function getByNamespace($namespace) {
		$this->validateNamespace($namespace);
		if (!$this->isNamespaceLoaded($namespace)) {
			$this->loadEntriesByNamespace($namespace);
		}
		return (array) $this->entries[$namespace];
	}
}
