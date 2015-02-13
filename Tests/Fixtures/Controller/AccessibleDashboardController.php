<?php
namespace Dkd\Contentdashboard\Tests\Fixtures\Controller;

use Dkd\Contentdashboard\Controller\DashboardController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Class AccessibleDashboardController
 */
class AccessibleDashboardController extends DashboardController {

	/**
	 * @param ViewInterface $view
	 * @return void
	 */
	public function setView(ViewInterface $view) {
		$this->view = $view;
	}

}

