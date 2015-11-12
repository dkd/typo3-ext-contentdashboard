<?php
namespace Dkd\Contentdashboard\ViewHelpers\Cmis;

use Dkd\CmisService\Factory\CmisObjectFactory;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetObjectViewHelper
 */
class GetObjectViewHelper extends AbstractViewHelper {
	/**
	 * @param string $cmisObjectId
	 * @return string
	 */
	public function render($cmisObjectId = NULL) {
		if ($cmisObjectId === NULL) {
			$cmisObjectId = $this->renderChildren();
		}
		$sessionFactory = new CmisObjectFactory();
		$session = $sessionFactory->getSession();

		$cmisObject = $session->getObject(
			$session->createObjectId($cmisObjectId)
		);

		return $cmisObject;
	}
}
