<?php
function YahooWeather_loader_start()
{
	global $loader_modules_styles;
	$loader_modules_styles[] = 'modules/YahooWeather/styles/YahooWeather.css';
}
/**
 * Yahoo Weather page
 *
 * @since 1.2.1
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Modules
 * @author Dimitri Schreiber
 */

function YahooWeather($citykey='GMXX2254',$cityname ='Altötting', $correct=-60, $time_difference=1)
{
include("/modules/YahooWeather/YahooWeather.php"); 
try {
 $weather = new YahooWeather($citykey,$correct,$time_difference);
} 
catch(Exception $e) {
 echo "Caught exception: ".$e->getMessage();
 exit();
}
if (!empty($weather->forecast)) {
  $weekdays = array (l('sun', '_YahooWeather'),l('mon', '_YahooWeather'),l('tue', '_YahooWeather'),l('wed', '_YahooWeather'),l('thu', '_YahooWeather'),l('fri', '_YahooWeather'),l('sat', '_YahooWeather'));
 
 echo'<h2 class="desc">'.l('desc', '_YahooWeather').'</h2>';

echo'<h3 class="weather">Aktuelle Wetterlage in München am heutigen '.$weekdays[date("w",$weather->forecast[0][date])].', '. date("d.m.Y",$weather->forecast[0][date]).'</h3>';

                      echo'<div class="weather_image">
                <img class="zoom" src="'.$weather->url.'" title="'. $weather->convert_condition($weather->condition_code) .'" alt="'. $weather->convert_condition((int)$weather->condition_code) .'"><br>'. $weather->convert_condition($weather->condition_code) .'</div>';
 echo'<div class="weather_temperature">
                <span class="current_temperature">'.$weather->temp.' °C</span><br>
				влажность: '.$weather->humidity.'%</div>	';

			                     echo'<div class="weather_humidity">
                <span class="weather_label">давление: </span>'.sprintf("%d",$weather->pressure*0.75006375541921).' мм.рт.ст., '.$weather->get_rising().'<br>
				<span class="weather_label">ветер: </span>'.sprintf("%d",$weather->wind_speed*1000/3600).' м/с ('.$weather->wind_speed.' км/ч), 
   '.$weather->get_wind_direction().'<br>
                <span class="weather_label">восход Солнца: </span>'.date("H:i",$weather->correct_minutes($weather->sunrise)).', закат: ',date("H:i",$weather->correct_minutes($weather->sunset)).'</div>';	
 echo'<h3 class="weather">Прогноз погоды  на 4 дня</h3>';
echo'<div class="weather_forecasts">';
 for($i=1; $i <= 4; $i++) {
 echo'<div class="weather_forecast">

        <div class="weather_forecast_day"><strong>'.$weekdays[date("w",$weather->forecast[$i][date])].'</strong><br>
		'. date("d.m.Y",$weather->forecast[$i][date]).'</div>


        <div class="weather_forecast_image"><img class="zoom" src="modules/YahooWeather/images/'.$weather->forecast[$i][code].'.png" title="'. $weather->convert_condition($weather->forecast[$i][code]) .'" alt="'. $weather->convert_condition($weather->forecast[$i][code]) .'"></div>
	
        <div class="weather_forecast_condition">'.l('forecast', '_YahooWeather').'<br>
		'. $weather->convert_condition($weather->forecast[$i][code]) .'</div>
		
		<div class="weather_forecast_high">
            Макс.: '.$weather->forecast[$i][high].'°C<br>
			Мин.: '.$weather->forecast[$i][low].'°C        </div>



</div>';
 }
 echo '</div>';
}
else {
 echo '<div>Не найдено данных для кода "'.$citykey.'"</div>';
}

//echo "<pre>";print_r($weather);echo "</pre>";

}



