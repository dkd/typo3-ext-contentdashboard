<?php
namespace Dkd\Contentdashboard\ViewHelpers;

use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class SortArrayViewHelper
 */
class SortArrayViewHelper extends AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('array', 'array', 'The array to be sorted', FALSE, NULL);
		$this->registerArgument('sortBy', 'string', 'The property to sort by', TRUE);
		$this->registerArgument('direction', 'string', 'The sorting direction', TRUE);
	}

	/**
	 * @return array
	 */
	public function render() {
		$array = isset($this->arguments['array']) ? $this->arguments['array'] : $this->renderChildren();
		if (empty($this->arguments['sortBy'])) {
			return $array;
		}
		usort($array, array($this, 'sortByProperty'));
		if ($this->arguments['direction'] === 'desc') {
			$array = array_reverse($array, FALSE);
		}
		return $array;
	}

	/**
	 * @param CmisObjectInterface $a
	 * @param CmisObjectInterface $b
	 * @return integer
	 */
	public function sortByProperty(CmisObjectInterface $a, CmisObjectInterface $b) {
		$propertyOne = $a->getPropertyValue($this->arguments['sortBy']);
		$propertyTwo = $b->getPropertyValue($this->arguments['sortBy']);
		if ($propertyOne === $propertyTwo) {
			return 0;
		}
		$candidates = array($propertyOne, $propertyTwo);
		asort($candidates, SORT_NATURAL);
		$candidates = array_values($candidates);
		return (array_search($propertyOne, $candidates) < array_search($propertyTwo, $candidates) ? -1 : 1);
	}

}
