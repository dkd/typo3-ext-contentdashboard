<?php
namespace Dkd\Contentdashboard\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class DashboardAjaxController
 */
class DashboardAjaxController extends ActionController {

	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'TYPO3\\CMS\\Extbase\\Mvc\\View\\JsonView';

	/**
	 * @param string $objectId The ID of a CMIS object to be displayed
	 * @return void
	 */
	public function lifeCycleDataAction($objectId) {
		// TODO implement to get data from influxDB
		$data = json_decode('{"nodes": [
			{"name": "Letter 1", "date":"2015-03-14", "id":"A", "from":"R", "type":"fixed"},
			{"name": "Letter 2", "date":"2015-03-16", "id":"V", "from":"F", "type":"fixed"},
			{"name": "Letter 2", "date":"2015-03-16", "id":"V", "from":"F", "type":"fixed"},
			{"name": "Letter 4", "date":"2015-03-30", "id":"V", "from":"R", "type":"fixed"},
			{"name": "Letter 5", "date":"2015-03-30", "id":"V", "from":"R", "type":"fixed"},
			{"name": "Letter 6", "date":"2015-04-01", "id":"R", "from":"F", "type":"fixed"},
			{"name": "Letter 8", "date":"2015-03-30", "id":"V", "from":"R", "type":"fixed"},
			{"name": "Letter 10", "date":"2015-03-30", "id":"A", "from":"R", "type":"fixed"},
			{"name": "Letter 11", "date":"2015-04-02", "id":"V", "from":"R", "type":"fixed"}],
			"links": []}', TRUE);

		$this->view->assign('value', $data);
	}
}
