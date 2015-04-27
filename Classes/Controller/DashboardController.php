<?php
namespace Dkd\Contentdashboard\Controller;

use Dkd\PhpCmis\Data\DocumentInterface;
use GuzzleHttp\Exception\RequestException;
use Maroschik\Identity\IdentityMap;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use GuzzleHttp\Client;

/**
 * Class DashboardController
 */
class DashboardController extends AbstractController
{
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
	 * @param string $cmisObjectId
	 * @param string $folderId
	 */
	public function preserveAction($cmisObjectId, $folder = NULL) {
		// TODO Move this to the forgetit extension
		$client = new Client();
		try {
			$response = $client->post(self::MIDDLEWARE_URL . 'resource', array(
				'body' => array(
					'cmisServerId' => self::CMIS_SERVER_ID,
					'cmisId' => $cmisObjectId,
					// TODO calculate preservation value
					'PV' => 1
				)
			))->json();
			$this->addFlashMessage('Task to preserve resource has been triggered with id ' . $response['taskId']);
		} catch (RequestException $e) {
			$this->addFlashMessage('Error while preserving the resource. Details: ' . $e->getMessage(), '', AbstractMessage::ERROR);
		}
		$this->forward('index', NULL, NULL, array('folder' => $folder));
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
}
