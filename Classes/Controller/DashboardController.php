<?php
namespace Dkd\Contentdashboard\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class DashboardController
 */
class DashboardController extends ActionController {

	/**
	 * @return void
	 */
	public function indexAction() {
		$objectDummies = array_fill(0, 40, 0);
		$objectDummies = array_map(function() {
			$objectId = uniqid('o');
			return array(
				'objectId' => $objectId,
				'title' => 'Fake object #' . $objectId,
				'preservation' => rand(-1, 5),
				'buoyancy' => rand(-1, 5)
			);
		}, $objectDummies);
		$this->view->assign('objects', $objectDummies);
	}

	/**
	 * @param string $objectId The ID of a CMIS object to be displayed
	 * @return void
	 */
	public function detailAction($objectId) {
		$this->view->assign('object', array(
			'objectId' => $objectId,
			'Fake object #' . $objectId,
			'preservation' => rand(-1, 5),
			'buoyancy' => rand(-1, 5)
		));
	}

}
