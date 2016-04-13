<?php
namespace Dkd\Contentdashboard\ViewHelpers;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class MeterViewHelper
 */
class MbViewHelper extends AbstractViewHelper {

    /**
     * @param integer $value
     * @param integer $maximum
     * @return string
     */
    public function render($value, $maximum = 5) {
        $value = mt_rand() / mt_getrandmax();

        if (-1 === (integer) $value) {
            return $this->translate('dashboard.assets.meters.noValue');
        }

        $colorBegin = $this->partToColor(0);
        $colorEnd = $this->partToColor($value);
        $colorPercent = intval($value * 100) . '%';
        $html = '<div class="meter mbbox" style=""><div style="';
        $html .= "background: linear-gradient(to right, $colorBegin 0%, $colorEnd $colorPercent);";
        $html .= "width: $colorPercent;";
        $html .= 'height:100%;';
        $html .= '"></div></div>';
        $html .= $this->translate('dashboard.assets.meters.values.mb.' . intval($value*5+0.49));
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


    public function partToColor($part)
    {
        $r = intval($part*255);
        $g = 0;
        $b = intval((1-$part)*255);

        $r = dechex($r<0?0:($r>255?255:$r));
        $g = dechex($g<0?0:($g>255?255:$g));
        $b = dechex($b<0?0:($b>255?255:$b));

        $color = (strlen($r) < 2?'0':'').$r;
        $color .= (strlen($g) < 2?'0':'').$g;
        $color .= (strlen($b) < 2?'0':'').$b;

        return '#'.$color;
              }

}