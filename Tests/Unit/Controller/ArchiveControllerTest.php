<?php
namespace Dkd\Contentdashboard\Unit\Controller;

use Dkd\Contentdashboard\Controller\ArchiveController;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class ArchiveControllerTest
 */
class ArchiveControllerTest extends UnitTestCase {

	/**
	 * @return void
	 */
	public function testControllerReturnsNull() {
		$archiveController = new ArchiveController();
		$this->assertNull($archiveController->indexAction());
	}
}
