<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_fl_weather
 *
 * @copyright   Copyright (C) 2020 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;

$document = JFactory::getDocument();
$document->addStyleSheet("modules/mod_fl_weather/css/style.css");
$document->addStyleSheet("https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css");
$document->addScript("https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js");
$document->addScript("https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/3.6.95/css/materialdesignicons.css");
$document->addScript("https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js");

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', '.selectCity', null, array('placeholder_text_multiple' => 'Select cities'));
$cityOptions = array ();

foreach ($cityList as $key => $city)
{
    $cityOptions[] = JHtml::_('select.option', $city->city_id, $city->name . ' (' . $city->country . ')');
}

?>
<div class="page-content page-container" id="page-content">
    <div class="padding">
        <div class="row container d-flex justify-content-left">
            <div class="col-lg-6 grid-margin stretch-card">

                <form name="adminForm" id="adminForm" class="form-validate" method="post" action="">
                    <?php
                    echo JHtml::_(
                        'select.genericlist',
                        $cityOptions, "locations[]",
                        'class="input-small selectCity" multiple="true" onchange="this.form.submit();"',
                        "value",
                        "text",
                        $locations
                    );
                    ?>
                </form>

                <?php
                    if (!empty($locations))
                    {
                        foreach ($locations as $loc)
                        {
                        ?>
                            <!--weather card-->
                            <div class="card card-weather">
                                <div class="card-body" style="height: 270px;">
                                    <div class="weather-date-location">
                                        <h3><?php echo JHtml::_('date', '', 'l'); ?></h3>
                                        <p class="text-gray"> <span class="weather-date"><?php echo JHtml::_('date', '', 'd F Y'); ?></span> <span class="weather-location"><?php echo $cityList[$loc]->name; ?>, <?php echo $cityList[$loc]->country; ?></span> </p>
                                    </div>
                                    <div class="weather-data d-flex">
                                        <div class="mr-auto">
                                            <h4 class="display-3"><?php echo ModFlWeatherHelper::convertTemperature($locationWeathers['currentWeather'][$loc]->main->temp)?> <span class="symbol">°</span>C</h4>
                                            <p> <?php echo ucwords($locationWeathers['currentWeather'][$loc]->weather[0]->main) ?> </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="d-flex weakly-weather">
                                    <?php

                                       $foreCastDateArray = array ();

                                       for ($i = 1; $i <= 5; $i++)
                                       {
                                            $iteratedTime = new Date("now +$i day");

                                            foreach ($locationWeathers['fiveDayforcast'][$loc]->list as $forecast)
                                            {
                                                $tempDate = JHtml::_('date', $forecast->dt_txt, 'Y-m-d');

                                                if (!in_array($tempDate, $foreCastDateArray))
                                                {
                                                    array_push($foreCastDateArray, $tempDate);
                                                ?>
                                                    <div class="weakly-weather-item">
                                                        <p class="mb-0"> <?php echo JHtml::_('date', $iteratedTime, 'D'); ?> </p> <i class="mdi mdi-weather-cloudy"></i>
                                                        <p class="mb-0"> <?php echo ModFlWeatherHelper::convertTemperature($forecast->main->temp)?>° C</p>
                                                    </div>
                                                <?php
                                                break;
                                                }
                                            }
                                       }
                                       ?>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <!--weather card ends-->
                        <?php
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</div>