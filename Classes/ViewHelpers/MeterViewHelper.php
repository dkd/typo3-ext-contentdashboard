<?php
namespace Dkd\Contentdashboard\ViewHelpers;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class MeterViewHelper
 */
class MeterViewHelper extends AbstractViewHelper {

	/**
	 * @param integer $value
	 * @param integer $maximum
	 * @return string
	 */
	public function render($value, $maximum = 5) {
		if (-1 === (integer) $value) {
			return $this->translate('dashboard.assets.meters.noValue');
		}
		$html = '<ul class="meter">';
		for ($i = 0; $i < $maximum; $i++) {
			if ($i < $value) {
				$html .= '<li class="filled"></li>';
			} else {
				$html .= '<li class="empty"></li>';
			}
		}
		$html .= '</ul>';
		$html .= $this->translate('dashboard.assets.meters.values.' . $value);
		return $html;
	}

	/**
	 * @codeCoverageIgnore
	 * @param string $key
	 * @return NULL|string
	 */
	protected function translate($key) {
		return LocalizationUtility::translate($key, 'Contentdashboard');
	}

}
