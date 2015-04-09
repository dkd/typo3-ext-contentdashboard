<?php
namespace Dkd\Contentdashboard\Controller;

use Dkd\CmisService\Factory\CmisObjectFactory;
use Dkd\CmisService\Factory\ObjectFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class DashboardController
 */
class DashboardController extends ActionController {

	/**
	 * @var ObjectFactory
	 */
	protected $objectFactory;

	/**
	 * @return void
	 */
	public function initializeAction() {
		$this->objectFactory = new ObjectFactory();
	}

	/**
	 * @param string $folder The ID of a CMIS folder to be browsed
	 * @return void
	 */
	public function indexAction($folder = NULL) {
		$cmisObjectFactory = new CmisObjectFactory();
		$cmisSession = $cmisObjectFactory->getSession();

		// currently we use the cmis root folder
		// this could be a configurable setting in the future
		if ($folder === NULL) {
			$rootFolder = $cmisSession->getRootFolder();
		} else {
			// TODO check if it a folder
			$rootFolder = $cmisSession->getObject($cmisSession->createObjectId($folder));
		}

		$this->view->assign('folder', $rootFolder);
	}

	/**
	 * @param string $objectId The ID of a CMIS object to be displayed
	 * @return void
	 */
	public function detailAction($objectId) {
		$this->view->assign('object', array(
			'objectId' => $objectId
		));
	}

}
