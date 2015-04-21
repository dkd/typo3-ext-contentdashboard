<?php
namespace Dkd\Contentdashboard\Controller;

use Dkd\CmisService\Factory\CmisObjectFactory;
use Dkd\PhpCmis\Data\DocumentInterface;
use Dkd\PhpCmis\SessionInterface;
use Maroschik\Identity\IdentityMap;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use GuzzleHttp\Client;

/**
 * Class DashboardController
 */
class DashboardController extends ActionController
{
	const MIDDLEWARE_URL = 'http://pofmiddleware:8080/server/rest-api/';

	/**
	 * @var CmisObjectFactory
	 */
	protected $cmisObjectFactory;

	/**
	 * @return void
	 */
	public function initializeAction() {
		$this->cmisObjectFactory = new CmisObjectFactory();
	}

	/**
	 * @param string $folder The ID of a CMIS folder to be browsed
	 * @return void
	 */
	public function indexAction($folder = NULL) {
		$cmisSession = $this->getCmisSession();

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
	 * @return string
	 */
	public function detailAction($objectId) {
		$this->view->assign(
			'object',
			array(
				'objectId' => $objectId
			)
		);

		$jsonView = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Mvc\\View\\JsonView');
		$jsonView->setControllerContext($this->controllerContext);
		$jsonView->assign('value', array('content' => $this->view->render()));

		return $jsonView->render();
	}

	/**
	 * @param string $cmisObjectId
	 * @param string $typo3Identifier
	 * @return void
	 */
	public function deleteAction($cmisObjectId, $typo3Identifier = NULL) {
		$cmisSession = $this->getCmisSession();

		$cmisObject = $cmisSession->getObject($cmisSession->createObjectId($cmisObjectId));
		$folderObjectId = NULL;
		if ($cmisObject instanceof DocumentInterface) {
			// TODO we need special behavior for sys_file / FAL
			if ($typo3Identifier === NULL) {
				// try to get typo3 identifier from cmis object
				$typo3Identifier = $cmisObject->getPropertyValue('typo3:uuid');
			}

			if ($typo3Identifier !== NULL) {
				$identityMap = new IdentityMap();
				$resourceIdentifier = $identityMap->getResourceLocationForIdentifier($typo3Identifier);
				if ($resourceIdentifier !== NULL) {
					$this->deleteTypo3Record($resourceIdentifier['tablename'], $resourceIdentifier['uid']);
				}
			}

			// TODO preserve object in forgetit framework

			$folderObjectId = $cmisObject->getParents()[0]->getId();
//			$this->registerResourceInPof($cmisObject);
			$cmisObject->delete(TRUE);
		}
		$this->addFlashMessage('Deleted CMIS object "' . $cmisObject->getPropertyValue('cmis:name') . '"');
		$this->forward('index', NULL, NULL, array('folder' => $folderObjectId));
	}

	/**
	 * @return SessionInterface
	 */
	protected function getCmisSession() {
		return $this->cmisObjectFactory->getSession();
	}

	/**
	 * @param $tablename
	 * @param $uid
	 */
	protected function deleteTypo3Record($tablename, $uid) {
		$cmd[$tablename][$uid]['delete'] = 1;
		$tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');
		$tce->stripslashes_values = 0;
		$tce->start(array(), $cmd);
		$tce->process_cmdmap();
	}

//	/**
//	 * @param DocumentInterface $cmisDocument
//	 */
//	protected function registerResourceInPof(DocumentInterface $cmisDocument)
//	{
//		$client = new \GuzzleHttp\Client();
//
//		$response = $client->post(
//			self::MIDDLEWARE_URL . 'test/upload/resource',
//			array('body' => array(
//			  'file' => $cmisDocument->getContentStream()
//			))
//		);
//
//		$id = NULL;
//		$url = NULL;
//		foreach ($response->json()['entries']['entry'] as $entry) {
//			if ($entry['key'] === 'id') {
//				$id = $entry['value'];
//			}
//			if ($entry['key'] === 'URL') {
//				$url = $entry['value'];
//			}
//		}
//
//		$this->addFlashMessage(
//		  'File has been preserved in POF under "' . $url
//			. '" with ID "' . $id . '"'
//		);
//	}
}
