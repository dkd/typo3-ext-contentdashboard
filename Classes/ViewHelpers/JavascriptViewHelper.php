<?php
namespace Dkd\Contentdashboard\ViewHelpers;

/**
 * Class JavascriptViewHelper
 */
class JavascriptViewHelper extends AbstractResourcesViewHelper {

	/**
	 * Returns an array (for feeding into f:be.container) of
	 * stylesheet files that should be loaded. Will load an
	 * additional Bootstrap resource if TYPO3 version is
	 * below 7.0 which includes this resource natively.
	 *
	 * @return array
	 */
	public function render() {
		$path = $this->getPublicResourcePath();
		$stylesheets = array();
		if (TRUE === $this->isCoreVersionBelowSeven()) {
			$stylesheets[] = $path . 'Themes/Bootstrap/js/bootstrap.min.js';
		}

		$stylesheets[] = $path . 'Vendor/Javascript/d3.js';
		$stylesheets[] = $path . 'Vendor/Javascript/d3dateline.js';
		$stylesheets[] = $path . 'Javascript/Dashboard.js';

		return $stylesheets;
	}
}
