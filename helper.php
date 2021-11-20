<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_qlueweather
 *
 * @copyright   Copyright (C) 2020 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');


/**
 * Helper for mod_fl_weather
 *
 * @since  1.0.0
 */
class ModFlWeatherHelper
{
    public static $weatherAPI = "https://api.openweathermap.org/data/2.5/";

    /**
     * Retrieve list of cities
     *
     * @return  Array  List of City Objects
     *
     * @since   1.0.0
     */
    public static function getCityList()
    {
        // Get the dbo
        $db    = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select(array ('city_id', 'name', 'country'));
        $query->from($db->qn('#__fl_openweather_cities'));
        // $query->order($query->rand());
        // $query->setLimit(100);
        $db->setQuery($query);

        return $db->loadObjectList('city_id');
    }

    /**
     * Get current weather by city Id
     *
     * @param   INT                        $cityId   City Id
     *
     * @param   \Joomla\Registry\Registry  $params  Module parameters
     *
     * @return  mixed
     *
     * @since   1.0.0
     */
    public static function getCurrentWeather($cityId, $params)
    {
        $query = array ('id' => $cityId);
        $data  = ModFlWeatherHelper::callAPI("weather", $query, $params);

        if ($data->cod != 404)
        {
            return $data;
        }
    }

    /**
     * Get 5 day weather forecast by city Id
     *
     * @param   INT                        $cityId   City Id
     *
     * @param   \Joomla\Registry\Registry  $params  Module parameters
     *
     * @return  mixed
     *
     * @since   1.0.0
     */
    public static function getFiveDayForecast($cityId, $params)
    {
        $query = array ('id' => $cityId);
        $data  = ModFlWeatherHelper::callAPI("forecast", $query, $params);

        if ($data->cod != 404)
        {
            return $data;
        }
    }

    /**
     * Call API
     *
     * @param   String                     $requestUrl Request URL
     *
     * @param   Array                      $data Array of query params
     *
     * @param   \Joomla\Registry\Registry  $params  Module parameters
     *
     * @return  mixed
     *
     * @since   1.0.0
     */
    public static function callAPI($requestUrl, $data, $params) {
        $requestUrl  .= "?";
        $apikey      = "&appid=" . $params->get('apikey');
        $requestData = "";

        foreach ($data as $key => $value)
        {
            $requestData .= ("&" . $key . "=" . urlencode($value));
        }

        $URL = ModFlWeatherHelper::$weatherAPI . $requestUrl . $requestData . $apikey;

        $curl   = curl_init();
        curl_setopt($curl, CURLOPT_URL, $URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);

        return json_decode($output);
    }

    /**
     * Converts temperature to celsius
     *
     * @param  Float  $temperature  Temperature to convert
     *
     * @return Float
     */
    public static function convertTemperature($temperature)
    {
        return round(($temperature - 273.15));
    }
}
