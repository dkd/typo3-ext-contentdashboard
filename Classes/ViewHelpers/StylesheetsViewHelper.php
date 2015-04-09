<?php
namespace Dkd\Contentdashboard\ViewHelpers;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class StylesheetsViewHelper
 */
class StylesheetsViewHelper extends AbstractViewHelper {

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
			$stylesheets[] = $path . 'Themes/Bootstrap/css/bootstrap.min.css';
			$stylesheets[] = $path . 'Stylesheets/TYPO3-6-2-compatibility.css';
		}
		$stylesheets[] = $path . 'Stylesheets/Default.css';

		return $stylesheets;
	}

	/**
	 * @codeCoverageIgnore
	 * @return boolean
	 */
	protected function isCoreVersionBelowSeven() {
		return version_compare(VersionNumberUtility::getNumericTypo3Version(), '7.1.0', '<');
	}

	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	protected function getPublicResourcePath() {
		return '../' . ExtensionManagementUtility::siteRelPath('contentdashboard') . 'Resources/Public/';
	}

}
