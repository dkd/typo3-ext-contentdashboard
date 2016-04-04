<?php
namespace Dkd\Contentdashboard\ViewHelpers;

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Class SessionVariableViewHelper
 */
class SessionVariableViewHelper extends AbstractViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('set', 'string', 'The name of the session data set', TRUE);
		$this->registerArgument('name', 'string', 'The name of the sesssion variable to get - dotted path supported', TRUE);
	}

	/**
	 * @return mixed
	 */
	public function render() {
		return ObjectAccess::getPropertyPath(
			$this->getBackendUserAuthentication()->getSessionData($this->arguments['set']),
			$this->arguments['name']
		);
	}

	/**
	 * @return BackendUserAuthentication
	 */
	protected function getBackendUserAuthentication() {
		return $GLOBALS['BE_USER'];
	}

}
