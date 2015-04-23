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
			{"name": "Version from: 2015-03-24\n Autor: Edith Rode", "date":"2015-03-24", "label":"V", "from":"R", "type":"fixed", "id": "1"},
			{"name": "Version from: 2015-03-26\n Autor: Edith Rode", "date":"2015-03-26", "label":"V", "from":"R", "type":"fixed", "id": "2"},
			{"name": "Version from: 2015-03-27\n Autor: Edith Rode", "date":"2015-03-27", "label":"V", "from":"R", "type":"fixed", "id": "3"},
			{"name": "Archived from: 2015-03-28\n Autor: Edith Rode", "date":"2015-03-29", "label":"A", "from":"R", "type":"fixed", "id": "4"},
			{"name": "Restored from: 2015-03-30\n Autor: Edith Rode", "date":"2015-03-30", "label":"R", "from":"R", "type":"fixed", "id": "5"},
			{"name": "Version from: 2015-03-31\n Autor: Edith Rode", "date":"2015-03-31", "label":"V", "from":"R", "type":"fixed", "id": "6"},
			{"name": "Version from: 2015-04-01\n Autor: Edith Rode", "date":"2015-03-01", "label":"V", "from":"R", "type":"fixed", "id": "7"},
			{"name": "Version from: 2015-04-02\n Autor: Edith Rode", "date":"2015-04-02", "label":"V", "from":"R", "type":"fixed", "id": "8"},
			{"name": "Version from: 2015-04-04\n Autor: Edith Rode", "date":"2015-04-04", "label":"V", "from":"R", "type":"fixed", "id": "9"}],
			"links": []}', TRUE);

		$this->view->assign('value', $data);
	}
}
