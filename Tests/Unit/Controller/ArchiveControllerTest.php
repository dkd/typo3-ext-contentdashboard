<?php
namespace Dkd\Contentdashboard\Unit\Controller;

class ArchiveController extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @return void
	 */
	public function testControllerReturnsNull() {
		$archiveController = new \Dkd\Contentdashboard\Controller\ArchiveController();
		$this->assertNull($archiveController->indexAction());
	}
}