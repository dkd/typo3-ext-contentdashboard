<?php
namespace Dkd\Contentdashboard\ViewHelpers;

use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class MeterViewHelperTest
 */
class MeterViewHelperTest extends UnitTestCase {

	/**
	 * @dataProvider getRenderTestValues
	 * @param integer $value
	 * @param integer $maximum
	 * @return void
	 */
	public function testRender($value, $maximum) {
		$class = str_replace('Tests\\Unit\\', '', substr(get_class($this), 0, -4));
		$mock = $this->getMock($class, array('translate'));
		$mock->expects($this->once())->method('translate')->with('dashboard.assets.meters.values.' . $value);
		$result = $mock->render($value, $maximum);
		$this->assertEquals($maximum, substr_count($result, '<li'));
		$this->assertEquals($value, substr_count($result, '<li class="filled"'));
	}

	/**
	 * @return array
	 */
	public function getRenderTestValues() {
		return array(
			array(0, 5),
			array(1, 5),
			array(2, 5),
			array(3, 5),
			array(4, 5),
			array(5, 5),
			array(5, 10),
			array(6, 10),
			array(7, 10),
			array(8, 10),
			array(9, 10),
			array(10, 10)
		);
	}

	/**
	 * @dataProvider getNoValueBehaviorTestValues
	 * @parameter integer $maximum
	 * @return void
	 */
	public function testNoValueBehavior($maximum) {
		$class = str_replace('Tests\\Unit\\', '', substr(get_class($this), 0, -4));
		$mock = $this->getMock($class, array('translate'));
		$mock->expects($this->once())->method('translate')->with('dashboard.assets.meters.noValue');
		$mock->render(-1, $maximum);
	}

	/**
	 * @return array
	 */
	public function getNoValueBehaviorTestValues() {
		return array(
			array(1),
			array(2),
			array(3),
			array(4),
			array(5)
		);
	}

}
