<?php
namespace Dkd\Contentdashboard\ViewHelpers;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class MeterViewHelper
 */
class PvViewHelper extends AbstractViewHelper {

	/**
	 * @param integer $value
	 * @param integer $maximum
	 * @return string
	 */
    public function render($value, $maximum = 5) {
        $value = rand(1,5);
        if (-1 === (integer) $value) {
            return $this->translate('dashboard.assets.meters.noValue');
        }

        $color = ['black', 'brown', '#CD7F32', 'silver', 'gold'][$value-1];

        $html = '<ul class="meter">';
		for ($i = 0; $i < $maximum; $i++) {
			if ($i < $value) {
                $html .= '<li class="filled" style="background-color:' . $color . '"></li>';
			} else {
				$html .= '<li class="empty"></li>';
			}
		}
		$html .= '</ul>';
        $html .= $this->translate('dashboard.assets.meters.values.pv.' . $value);
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
