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
		$this->processTasks();
		$this->view->assign('objects', $this->getResources());
	}

	/**
	 * @param string $cmisId
	 * @return void
	 */
	public function deleteRestoreTaskAction($cmisId) {
		$this->getRegistry()->remove(self::REGISTRY_KEY_RESTORE_TASK, $cmisId);
	}

	/**
	 * Get all restore tasks and process them
	 *
	 * @TODO that must be done by some kind of scheduler in the future
	 *
	 * @return void
	 */
	protected function processTasks() {
		$client = new Client();
		$registry = $this->getRegistry();
		$tasks = $registry->getByNamespace(self::REGISTRY_KEY_RESTORE_TASK);
		foreach ($tasks as $cmisId => $taskInfo) {
			try {
				$newTaskInfo = $client->get(
					self::MIDDLEWARE_URL . 'task/' . $taskInfo['taskId'] . '/result'
				)->json();
			} catch (RequestException $e) {
				$this->addFlashMessage(
					'Error while fetching task info from Middleware. Details: ' . $e->getMessage(),
					NULL,
					AbstractMessage::ERROR
				);
				continue;
			}

			if ($taskInfo['taskStatus'] !== $newTaskInfo['task-status']) {
				$this->addFlashMessage(
					'Task status of task "' . $taskInfo['taskId'] .
						'" has been changed from "' . $taskInfo['taskStatus'] . '" to "' .
						$newTaskInfo['task-status'] . '". The task is related to CMIS id "' . $cmisId . '"',
					NULL,
					AbstractMessage::INFO
				);

				if ($newTaskInfo['task-status'] === 'COMPLETED') {
					$httpInvoker = new \GuzzleHttp\Client(
						array(
							'defaults' => array(
								'auth' => array(
									trim($newTaskInfo['task-result']['cmis-repo-username'], '""'),
									trim($newTaskInfo['task-result']['cmis-repo-password'], '""')
								)
							)
						)
					);

					$parameters = array(
						\Dkd\PhpCmis\SessionParameter::BINDING_TYPE => \Dkd\PhpCmis\Enum\BindingType::BROWSER,
						\Dkd\PhpCmis\SessionParameter::BROWSER_URL => str_replace(
							'atom11',
							'browser',
							trim($newTaskInfo['task-result']['atom-pub-url'], '""')
						),
						\Dkd\PhpCmis\SessionParameter::REPOSITORY_ID => trim(
							$newTaskInfo['task-result']['cmisServerId'],
							'""'
						),
						\Dkd\PhpCmis\SessionParameter::BROWSER_SUCCINCT => FALSE,
						\Dkd\PhpCmis\SessionParameter::HTTP_INVOKER_OBJECT => $httpInvoker
					);

					$sessionFactory = new \Dkd\PhpCmis\SessionFactory();
					$session = $sessionFactory->createSession($parameters);

					/** @var DocumentInterface $cmisObject */
					$cmisObject = $session->getObject(
						$session->createObjectId(trim($newTaskInfo['task-result']['cmisId'], '""'))
					);

					$tempFilePath = GeneralUtility::tempnam(
						'contentdashboard-restore-' . md5($cmisObject->getId()),
						'.' . pathinfo($cmisObject->getContentStreamFileName(), PATHINFO_EXTENSION)
					);

					$content = $cmisObject->getContentStream()->getContents();

					// store the restored item on disk
					GeneralUtility::writeFile($tempFilePath, $content);

					$extractPath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . '/' . pathinfo(
							$tempFilePath,
							PATHINFO_FILENAME
						);
					$phar = new \PharData($tempFilePath);
					$phar->extractTo($extractPath);

					$extractPathFolders = scandir($extractPath);
					$contentsPath = $extractPath . '/' . $extractPathFolders[2] . '/content';
					$contents = scandir($contentsPath);

					$content = $contentsPath . '/' . $contents[2];

					$typo3Cmis = $this->getCmisSession();
					$properties = array();

					$properties[PropertyIds::NAME] = time() . '_' . $contents[2];
					$properties[PropertyIds::OBJECT_TYPE_ID] = $cmisObject->getPropertyValue(
						PropertyIds::OBJECT_TYPE_ID
					);

					$newCmisId = $typo3Cmis->createDocument(
						$properties,
						$typo3Cmis->createObjectId($typo3Cmis->getRepositoryInfo()->getRootFolderId()),
						\GuzzleHttp\Stream\Stream::factory(fopen($content, 'r'))
					);

					$this->addFlashMessage(
						'Object has been restored in CMIS with id ' . $newCmisId->getId()
					);
					// remove task
					$registry->remove(self::REGISTRY_KEY_RESTORE_TASK, $cmisId);
				}
			}
		}
	}

	/**
	 * @param string $cmisId
	 * @return void
	 */
	public function restoreAction($cmisId) {
		// TODO Move this to the forgetit extension
		$registry = $this->getRegistry();

		$taskInfo = $registry->get(self::REGISTRY_KEY_RESTORE_TASK, $cmisId);
		if (isset($taskInfo['taskId'])) {
			$this->addFlashMessage(
				'Restore has not been triggered because a task already exists for this resource. Task ' . $taskInfo['taskId'],
				NULL,
				AbstractMessage::WARNING
			);
			$this->forward('index');
		}

		$client = new Client();
		$restoreResponse = $client->get(
			self::MIDDLEWARE_URL . 'restore?cmisServerId=' . self::CMIS_SERVER_ID . '&cmisId=' . $cmisId
		)->json();

		$taskInfo = $client->get(
			self::MIDDLEWARE_URL . 'task/' . $restoreResponse['taskId'] . '/result'
		)->json();

		$registry->set(
			self::REGISTRY_KEY_RESTORE_TASK,
			$cmisId,
			array(
				'taskId' => $restoreResponse['taskId'],
				'taskStatus' => $taskInfo['task-status']
			)
		);

		$this->addFlashMessage(
			'Restore has been triggered. Task has been created with id ' . $restoreResponse['taskId']
		);

		$this->forward('index');
	}

	/**
	 * TODO Move this to the forgetit extension
	 *
	 * @return array|mixed
	 */
	protected function getResources() {
		$resources = array();
		$registry = $this->getRegistry();
		try {
			$client = new Client();
			$response = $client->get(
				self::MIDDLEWARE_URL . 'resources/' . self::CMIS_SERVER_ID . '/'
			)->json();

			foreach ($response as $object) {
				$cmisServerId = $object['cmisServerId'];
				$cmisObjectId = $object['cmisId'];

				$details = $client->get(
					self::MIDDLEWARE_URL . 'resource?cmisServerId=' . $cmisServerId . '&cmisId=' . $cmisObjectId
				)->json();

				if (isset($details['metadata'])) {
					$metadata = array();
					foreach ($details['metadata']['metadata'] as $metaItem) {
						$metadata[str_replace('.', '-', $metaItem['key'])] = $metaItem['value'];
					}
					$details['metadata']['metadata'] = $metadata;

					$restoreTask = $registry->get(self::REGISTRY_KEY_RESTORE_TASK, $cmisObjectId);
					if ($restoreTask !== NULL) {
						$details['restoreTask'] = $restoreTask;
					} else {
						$details['restoreTask'] = NULL;
					}
					$resources[] = $details;
				}
			}
		} catch (RequestException $e) {
			$this->addFlashMessage(
				'Error while fetching resources from Middleware. Details: ' . $e->getMessage(),
				NULL,
				AbstractMessage::ERROR
			);
		}
		return $resources;
	}
}
