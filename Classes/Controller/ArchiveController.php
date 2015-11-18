<?php
namespace Dkd\Contentdashboard\Controller;

use Dkd\PhpCmis\Data\DocumentInterface;
use Dkd\PhpCmis\PropertyIds;
use GuzzleHttp\Exception\RequestException;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use GuzzleHttp\Client;

/**
 * Class ArchiveController
 */
class ArchiveController extends AbstractController
{

	/**
	 * @return void
	 */
	public function indexAction() {
		$tasksService = $this->objectManager->get('Dkd\\Pofconnector\\Service\\PoFMiddlewareTasksService');
		$tasksService->processTasks();

		$resourcesService = $this->objectManager->get('Dkd\\Pofconnector\\Service\\PoFMiddlewareResourcesService');

		$this->view->assign('objects', $resources = $resourcesService->getResources());
	}

	/**
	 * @param string $cmisId
	 * @return void
	 */
	public function deleteRestoreTaskAction($cmisId) {
		$tasksService = $this->objectManager->get('Dkd\\Pofconnector\\Service\\PoFMiddlewareTasksService');
		$tasksService->deleteRestoreTaks($cmisId);
	}

	/**
	 * @param string $cmisId
	 * @return void
	 */
	public function restoreAction($cmisId) {
		$preservationService = $this->objectManager->get('Dkd\\Pofconnector\\Service\\PoFMiddlewarePreservationService');
		$preservationService->restore($cmisId);

		$this->forward('index');
	}
}
