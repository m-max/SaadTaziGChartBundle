<?php

namespace SaadTazi\GChartBundle\Chart;

use SaadTazi\GChartBundle\DataTable\DataTable;
/**
 * class to generate BarCharts images
 *
 * can add
 *  - margin (chma)
 *  - Axis Styles and Labels : https://developers.google.com/chart/image/docs/gallery/bar_charts#gcharts_axis_styles_labels
 *  - Better Background Fills (chf)
 *  - Grid Lines (chg)
 *  - Dynamic Icon Markers (chem)
 *  - Bar Width and Spacing (chbh)
 *
 * @link https://developers.google.com/chart/image/docs/gallery/bar_charts
 */
class GroupedBarChart extends BaseChart {
    const CHART_TYPE = 'bvg';

    protected $defaults = array(
        'width'  => 400,
        'height' => 200,
        'titleColor'  => '000000',
        'titleAlignment' => 'c',// chts
        'withLabels' => true,
        'withLegend'  => true,
        'legendAlignment' => 'r',// chdlp
        'legendOrder' => 'a',// chdlp
        'legendColor' => null,
        'legendSize' => null,
        'transparent' => false,
        'serieColor' => '0000FF',// chco
        'axis' => 'x, y',// chxt
        'axisScale' => 'a',// chds
        'barWidth' => 'a',
        'widthBarLabel' => true,
    );

    public function __construct(array $options = array()) {
        $this->options = array_merge($options, $this->defaults);

    }

    /**
     * Returns a URL for the Google Image Chart
     *
     * @param DataTable $data (labels are keys... if associative array)
     * @param integer $width
     * @param integer $height
     * @param array $color
     * @param string $title
     * @return string the Google Image Chart URL of the PieChart
     */
    public function getUrl(DataTable $data, $width, $height, $title = null, $params = array(), $rawParams = array()) {

        $title = isset($title) ? str_replace(' ', '+', $title): null;
        $titleSize  = isset($params['titleSize']) ? $params['titleSize']: null;
        $titleColor  = isset($params['titleColor']) ? $params['titleColor']: $this->options['titleColor'];
        $titleAlignment = isset($params['titleAlignment']) ? $params['titleAlignment']: $this->options['titleAlignment'];

        $legendAlignment = isset($params['legendAlignment']) ? $params['legendAlignment']: $this->options['legendAlignment'];
        $legendOrder = isset($params['legendOrder']) ? $params['legendOrder']: $this->options['legendOrder'];
        $legendColor = isset($params['legendColor']) ? $params['legendColor']: $this->options['legendColor'];
        $legendSize = isset($params['legendSize']) ? $params['legendSize']: $this->options['legendSize'];

        $transparent = isset($params['transparent']) ? $params['transparent']: $this->options['transparent'];
        $backgroundFillColor = isset($params['backgroundFillColor']) ? $params['backgroundFillColor']: null;
        $axis = isset($params['axis']) ? $params['axis']: null;
        $axisScale = isset($params['axisScale']) ? $params['axisScale']: $this->options['axisScale'];
        $barWidth = isset($params['barWidth']) ? $params['barWidth']: $this->options['barWidth'];
        $serieColor = isset($params['serieColor']) ? : null;
        if (is_array($serieColor)) {
            $color = implode('|', $serieColor);
        } else {
            $color = $serieColor? $serieColor: null;
        }

        $withBarLabel = isset($params['withBarLabel']) ? $params['withBarLabel']: $this->options['withBarLabel'];
        $chm = null;
        if($withBarLabel){
            $chm = 'N,000000,0,-1,11';
        }

        $legendString = null;
        $withLegend = isset($params['withLegend']) ? $params['withLegend']: $this->options['withLegend'];
        if($withLegend) {
            $legendString = $this->getLegendParamValue($data);
        }

        $labels = null;
        $withLabels = isset($params['withLabels']) ? $params['withLabels']: $this->options['withLabels'];
        if ($withLabels) {
            $labels = implode('|', $data->getLabels());
        }

        $dataString = $this->getValueParamValue($data);

        //backgroundFill
        if (!is_null($backgroundFillColor)) {
            $chf = 'bg';
            if ($transparent) {
                $chf = 'a';
            }
            $chf = $chf . ',s,' . $backgroundFillColor;
        }

        $params = array(
            'chd' => $dataString,
            'cht'  => static::CHART_TYPE,
            'chs'  => $width . 'x' . $height,
            'chl'  => isset($labels)? $labels : null,
            'chco' => $color,
            'chtt' => $title,
            'chts' => $titleColor . ',' . $titleSize . ',' . $titleAlignment,
            'chdl' => isset($legendString)? $legendString : null,
            'chdlp' => $legendAlignment . '|' . $legendOrder,
            'chdls' => $legendColor . '|' . $legendSize,
            'chf'  => isset($chf)? $chf : null,
            'chds' => $axisScale,
            'chxt' => $axis,
            'chbh' => $barWidth,
            'chm' => $chm
        );

        $params = array_merge($params, $rawParams);

        return self::BuildUrl($params);
    }
    public function getLegendParamValue($data) {
        return $this->getValueParamForPos($data, 0, '|');
    }
    public function getValueParamValue($data) {
        return 't:' . $this->getValueParamForPos($data, 1);
    }
    protected function getValueParamForPos(DataTable $data, $pos, $separator = ',') {
        $values = $data->getValuesForPosition($pos);
        //urlEncode...
        array_walk($values, '\SaadTazi\GChartBundle\Chart\BaseChart::urlEncode');
        return implode($separator, $values);


    }


}
