<?php
namespace Dkd\Contentdashboard\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class FlipArrayViewHelper
 */
class FlipArrayViewHelper extends AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('array', 'array', 'The array to be sorted', FALSE, NULL);
	}

	/**
	 * @return array
	 */
	public function render() {
		return array_flip(isset($this->arguments['array']) ? $this->arguments['array'] : $this->renderChildren());
	}

}
