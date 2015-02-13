<?php
namespace Dkd\Contentdashboard\Unit\Controller;

use Dkd\Contentdashboard\Tests\Fixtures\Controller\AccessibleDashboardController;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class DashboardControllerTest
 */
class DashboardControllerTest extends UnitTestCase {

	/**
	 * @return void
	 */
	public function testIndexAction() {
		$subject = new AccessibleDashboardController();
		$view = $this->getMock('TYPO3\\CMS\\Fluid\\View\\StandaloneView', array('assign'), array(), '', FALSE);
		$view->expects($this->once())->method('assign')->with('objects', $this->anything());
		$subject->setView($view);
		$subject->indexAction();
	}

	/**
	 * @return void
	 */
	public function testDetailAction() {
		$subject = new AccessibleDashboardController();
		$view = $this->getMock('TYPO3\\CMS\\Fluid\\View\\StandaloneView', array('assign'), array(), '', FALSE);
		$view->expects($this->once())->method('assign')->with('object', $this->anything());
		$subject->setView($view);
		$subject->detailAction('fakeobjectid');
	}

}
