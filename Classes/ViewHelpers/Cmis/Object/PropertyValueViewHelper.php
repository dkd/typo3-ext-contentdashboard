<?php
namespace Dkd\Contentdashboard\ViewHelpers\Cmis\Object;

use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class PropertyValuesViewHelper
 */
class PropertyValueViewHelper extends AbstractViewHelper {
	/**
	 * @param CmisObjectInterface $cmisObject
	 * @param string $property
	 * @return string
	 */
	public function render(CmisObjectInterface $cmisObject, $property) {
		$property = $cmisObject->getProperty($property);
		$propertyValue = NULL;
		if ($property !== NULL) {
			$propertyValue = $property->getFirstValue();
		}
		return $propertyValue;
	}
}
