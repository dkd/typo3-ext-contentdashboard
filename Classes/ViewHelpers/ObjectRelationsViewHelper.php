<?php
namespace Dkd\Contentdashboard\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ObjectTagsViewHelper
 */
class ObjectRelationsViewHelper extends AbstractViewHelper {

	/**
	 * @param string $cmisObjectId
	 * @return array
	 */
	public function render($cmisObjectId) {
		// TODO get relations for object
		// dummy data
		$relations = array(
			array(
				'type' => 'Page',
				'path' => 'en/examplepage/gallery'
			),
			array(
				'type' => 'Page',
				'path' => 'en/examplepage/shop'
			)
		);
		return $relations;
	}
}
