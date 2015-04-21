<?php
namespace Dkd\Contentdashboard\Controller;

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use GuzzleHttp\Client;

/**
 * Class ArchiveController
 */
class ArchiveController extends ActionController {

	/**
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('objects', $this->getResources());
	}

	/**
	 * TODO Move this to the forgetit extension
	 * @return array|mixed
	 */
	protected function getResources() {
		$response = array();
		try {
			$client = new Client();
			$response = $client->get(DashboardController::MIDDLEWARE_URL . 'resources/dkd/')->json();
		} catch (\Exception $e) {
			$this->addFlashMessage(
				'Error while fetching resources from Middleware. Details: ' . $e->getMessage(),
				NULL,
				AbstractMessage::ERROR
			);
		}
		return $response;
	}
}
