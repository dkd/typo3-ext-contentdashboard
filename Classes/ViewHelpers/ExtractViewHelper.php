<?php
namespace Dkd\Contentdashboard\ViewHelpers;

use Dkd\CmisService\Factory\ObjectFactory;
use Dkd\PhpCmis\Enum\RelationshipDirection;
use Dkd\PhpCmis\SessionInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Class ExtractViewHelper
 */
class ExtractViewHelper extends AbstractViewHelper {

	/**
	 * @param string $propertyName
	 * @param \ArrayAccess $object
	 * @return mixed
	 */
	public function render($propertyName, $object) {
		return reset(ObjectAccess::getPropertyPath($object->getProperties()->getProperties(), $propertyName)->getValues());
	}

	/**
	 * @return SessionInterface
	 */
	protected function getCmisSession() {
		return ObjectFactory::getInstance()->getCmisService()->getCmisSession();
	}

}
