<?php
namespace Dkd\Contentdashboard\ViewHelpers\Format;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class HtmlIdViewHelper
 */
class HtmlIdViewHelper extends AbstractViewHelper {
	/**
	 * @param mixed $value The value to output
	 * @return string
	 */
	public function render($value = NULL) {
		if ($value === NULL) {
			$value = $this->renderChildren();
		}
		return  preg_replace('/[^a-zA-Z0-9_\-]/', '', $value);
	}
}
