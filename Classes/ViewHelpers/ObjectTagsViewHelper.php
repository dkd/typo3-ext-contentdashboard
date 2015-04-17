<?php
namespace Dkd\Contentdashboard\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ObjectTagsViewHelper
 */
class ObjectTagsViewHelper extends AbstractViewHelper {

	/**
	 * @param string $cmisObjectId
	 * @return array
	 */
	public function render($cmisObjectId) {
		// TODO get tags for object

		//dummy tags
		$tags = array('picture', 'image', 'PNG', 'pic');

		return $tags;
	}
}
