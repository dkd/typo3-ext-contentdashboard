<?php
namespace Dkd\Contentdashboard\Controller;

use Dkd\Aggregation\Utility\UsageReader;
use Dkd\CmisService\Factory\CmisObjectFactory;
use Dkd\CmisService\Factory\ObjectFactory;
use Dkd\PhpCmis\Data\DocumentInterface;
use Dkd\PhpCmis\Data\FolderInterface;
use Dkd\PhpCmis\Exception\CmisObjectNotFoundException;
use GuzzleHttp\Exception\RequestException;
use Maroschik\Identity\IdentityMap;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use GuzzleHttp\Client;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Class DashboardController
 */
class DashboardController extends AbstractController
{
	/**
	 * @param $folderId
	 * @param null $default
	 * @return FolderInterface
	 */
	protected function getCurrentFolderObject($folderId, $default = NULL) {
		if ($folderId === NULL) {
			$folderId = $default;
		}
		try {
			$cmisSession = $this->getCmisSession();
			if (empty($folderId)) {
				return $cmisSession->getRootFolder();
			}
			$objectId = $cmisSession->createObjectId($folderId);
			return $cmisSession->getObject($objectId);
		} catch (CmisObjectNotFoundException $error) {
			if ($folderId === NULL) {
				return $cmisSession->getRootFolder();
			}
		}
		return $cmisSession->getObject($objectId);
	}

	/**
	 * @param string $sortBy
	 * @param string $direction
	 */
	public function setSortingAction($sortBy, $direction) {
		$referrer = $this->request->getInternalArgument('__referrer');
		$this->getBackendUserAuthentication()->setAndSaveSessionData(
			'contentdashboard_sorting', array(
				'sortBy' => $sortBy,
				'direction' => $direction
			)
		);
		$this->redirect(
			$referrer['@action'],
			$referrer['@controller'],
			$referrer['@extension']
		);
	}

	/**
	 * @param string $folder The ID of a CMIS folder to be browsed
	 * @return string
	 */
	public function indexAction($folder = NULL) {
		// Note: the page UID here is the only trustworthy way of reading this particular
		// argument. It is not possible to get this as part of the Extbase MVC Request.
		// Note also that in templates, the parameter is also handled in a separate manner.
		$pageUid = (integer) GeneralUtility::_GP('id');
		if ($pageUid > 0) {
			try {
				$default = ObjectFactory::getInstance()->getCmisService()->getUuidForLocalRecord('pages', $pageUid);
			} catch (CmisObjectNotFoundException $error) {
				$default = NULL;
			}
		}

		$currentFolder = $this->getCurrentFolderObject($folder, $default);

		$this->view->assign('folder', $currentFolder);

		//we are showing details on ourselves, so get that data
		$this->assignDetailValues($currentFolder->getId());
	}

	/**
	 * @param string $folder
	 * @return string
	 */
	public function filesAction($folder = NULL) {
		$default = $this->getCmisFalStorageRecordFolderConfigurationOption();

		$this->view->assign('folder', $this->getCurrentFolderObject($folder, $default));
	}

	/**
	 * @param string $objectId The ID of a CMIS object to be displayed
	 * @return null
	 */
	public function assignDetailValues($objectId) {
		$session = $this->getCmisSession();
		/** @var \Dkd\Aggregation\Service\InfluxDbService $influxDbService */
		$influxDbService = $this->objectManager->get('Dkd\\Aggregation\\Service\\InfluxDbService');
		$this->view->assign('socialCounter', $influxDbService->getSocialCounters($objectId));
		$this->view->assign('updateHistoryData', $influxDbService->getUpdateHistoryData($objectId));
		$object = $session->getObject($session->createObjectId($objectId));
		if ($object instanceof DocumentInterface) {
			//$this->view->assign('versions', $object->getAllVersions());
		}
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

		$this->assignDetailValues($objectId);

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
			$this->signalSlotDispatcher->dispatch(__CLASS__, 'afterDeleteAction', array(
				$typo3Identifier,
				$cmisObject->getId()
			));
		}
		$this->addFlashMessage('Deleted CMIS object "' . $cmisObject->getPropertyValue('cmis:name') . '"');
		$this->forward('index', NULL, NULL, array('folder' => $folderObjectId));
	}

	/**
	 * @param string $cmisObjectId
	 * @param string $folderId
	 */
	public function preserveAction($cmisObjectId, $folder = NULL) {
		try {
			// TODO calculate preservation value
			$preservationValue = 1;
			$preservationService = $this->objectManager->get('Dkd\\Pofconnector\\Service\\PoFMiddlewarePreservationService');
			$response = $preservationService->preserve($cmisObjectId, $preservationValue);

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

	/**
	 * @return array|NULL
	 */
	protected function getCmisFalStorageRecordFolderConfigurationOption() {
		$condition = 'driver = \'cmis\' AND is_online = 1 AND hidden = 0 AND deleted = 0';
		$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('configuration', 'sys_file_storage', $condition);
		if ($record) {
			$configuration = GeneralUtility::xml2array($record['configuration']);
			return $configuration['data']['sDEF']['lDEF']['folder']['vDEF'];
		}
		return NULL;
	}

	/**
	 * @return BackendUserAuthentication
	 */
	protected function getBackendUserAuthentication() {
		return $GLOBALS['BE_USER'];
	}
}
