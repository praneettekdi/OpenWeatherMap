<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_fl_weather
 *
 * @copyright   Copyright (C) 2020 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

require_once dirname(__FILE__) . '/helper.php';

$input     = JFactory::getApplication()->input;
$locations = $input->get('locations', array(), 'Array');
$cityList  = ModFlWeatherHelper::getCityList();

if (!empty($locations))
{
    $locationWeathers = array ();

    foreach ($locations as $key => $value)
    {
        $locationWeathers['currentWeather'][$value] = ModFlWeatherHelper::getCurrentWeather($value, $params);
        $locationWeathers['fiveDayforcast'][$value] = ModFlWeatherHelper::getFiveDayForecast($value, $params);
    }
}

require JModuleHelper::getLayoutPath('mod_fl_weather', $params->get('layout', 'default'));
