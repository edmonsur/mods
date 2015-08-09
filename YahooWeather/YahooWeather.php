<?php

class YahooWeather {
 // Ветер
 public $wind_chill;
 public $wind_direction;
 public $wind_speed;
 // Атмосферные показатели
 public $humidity;
 public $visibility;
 public $pressure;
 public $rising;
 //Время восхода и заката (формат unix time)
 public $sunrise;
 public $sunset;
 public $correct_minutes; //Коррекция возвращаемого времени в минутах (если надо)
 // Текущая температура воздуха и погода
 public $temp;
 public $condition_text;
 public $condition_code;
 //Картинка погода
 public $url;
 // Прогноз погоды на следующие дни
 public $forecast;
 //Единицы измерений
 public $units;
	
 function __construct($code, $correct = 0,$time_difference=0, $units = 'c', $lang = 'en') {
  $this->units = ($units == 'c')?'c':'f';
  $this->correct_minutes = $correct;
  $url = 'http://xml.weather.yahoo.com/forecastrss?p='.$code.'&u='.$this->units; //.'&d=5';
  $xml_contents = @file_get_contents($url);
  if($xml_contents === false) throw new Exception('Error loading '.$url);
  $xml = new SimpleXMLElement($xml_contents);
  // Ветер
  $tmp = $xml->xpath('/rss/channel/yweather:wind');
  if ($tmp === false) {
   throw new Exception("Error parsing XML.");
   return false;
  }
  if (empty($tmp[0])) return false;
  $tmp = $tmp[0];
  $this->wind_chill = (int)$tmp['chill'];
  $this->wind_direction = (int)$tmp['direction'];
  $this->wind_speed = (int)$tmp['speed'];
  // Атмосферные показатели
  $tmp = $xml->xpath('/rss/channel/yweather:atmosphere');
  if ($tmp === false) {
   throw new Exception("Error parsing XML.");
   return false;
  }
  $tmp = $tmp[0];
  $this->humidity = (int)$tmp['humidity'];
  $this->visibility = (int)$tmp['visibility'];
  $this->pressure = (int)$tmp['pressure'];
  $this->rising = (int)$tmp['rising'];
  // Время восхода и заката в unix time
  $tmp = $xml->xpath('/rss/channel/yweather:astronomy');
  if ($tmp === false) {
   throw new Exception("Error parsing XML.");
   return false;
  }
  $tmp = $tmp[0];
  $this->sunrise = $this->plus_minus($time_difference,strtotime($tmp['sunrise']));
  $this->sunset = $this->plus_minus($time_difference,strtotime($tmp['sunset']));
  // Текущая температура воздуха и погода
  $tmp = $xml->xpath('/rss/channel/item/yweather:condition');
  if ($tmp === false) {
   throw new Exception("Error parsing XML.");
   return false;
  }
  $tmp = $tmp[0];
  $this->temp = (int)$tmp['temp'];
  $this->condition_text = strtolower((string)$tmp['text']);
  $this->condition_code = (int)$tmp['code'];
  // Картинка
  $tmp = $xml->xpath('/rss/channel/item/description');
  if ($tmp === false) {
   throw new Exception("Error parsing XML.");
   return false;
  }
  $tmp = (string)$tmp[0]; 
  $res0=preg_match_all ("/(http:\\/\\/)?([a-z_0-9-.]+\\.[a-z]{2,3}(([ \"'>\r\n\t])|(\\/([^ \"'>\r\n\t]*)?)))/",$tmp,$res);
   //Просто парсим URL первой картинки из текста!
  if ($res0!==false and $res0>0) $this->url = preg_replace('#http:\/\/l\.yimg\.com\/a\/i\/us\/we\/52\/(.*?)\.gif#si','modules/YahooWeather/images/\1.png',$res[0][0]);
  else $this->url = '';
	
  // Прогноз погоды на следующие дни
  $forecast = array();
  $tmp = $xml->xpath('/rss/channel/item/yweather:forecast');
  if ($tmp === false) {
   throw new Exception("Error parsing XML.");
   return false;
  }
  foreach($tmp as $day) {
   $this->forecast[] = array(
    'date' => strtotime((string)$day['date']),
    'low' => (int)$day['low'],
    'high' => (int)$day['high'],
    'text' => (string)$day['text'],
    'code' => (int)$day['code']
   );
  }
  return true;
 }
	
 public function convert_condition ($code) {
  $cond = array (
   '0'=>'торнадо',
   '1'=>'шторм',
   '2'=>'ураган',
   '3'=>'сильная гроза',
   '4'=>'гроза',
   '5'=>'дождь со снегом',
   '6'=>'дождь с мокрым снегом',
   '7'=>'мокрый снег',
   '8'=>'ледяная пыль',
   '9'=>'морось',
   '10'=>'дождь, переходящий в снег',
   '11'=>'дождь',
   '12'=>'дождь',
   '13'=>'слабый снег',
   '14'=>'небольшой снег',
   '15'=>'метель',
   '16'=>'снег',
   '17'=>'град',
   '18'=>'дождь со снегом',
   '19'=>'пыль',
   '20'=>'туман',
   '21'=>'дымка',
   '22'=>'смог',
   '23'=>'порывистый ветер',
   '24'=>'ветрено',
   '25'=>'холодно',
   '26'=>'облачно',
   '27'=>'сильная облачность',
   '28'=>'сильная облачность',
   '29'=>'переменная облачность',
   '30'=>'переменная облачность',
   '31'=>'ясно',
   '32'=>'солнечно',
   '33'=>'ясно',
   '34'=>'ясно',
   '35'=>'дождь с градом',
   '36'=>'жарко',
   '37'=>'местами грозы',
   '38'=>'временами грозы',
   '39'=>'временами грозы', //ливни?
   '40'=>'местами дожди',
   '41'=>'сильный снег',
   '42'=>'местами снегопады',
   '43'=>'сильный снегопад',
   '44'=>'переменная облачность',
   '45'=>'гроза',
   '46'=>'снегопад',
   '47'=>'местами грозы'
  );
  if (array_key_exists($code,$cond)) return $cond[$code];
  else return '? (код '.$code.')';
 }
 
 public function __toString() {
  $u = "°".(($this->units == 'c')?'C':'F');
  return $this->temp.' '.$u.', '.$this->convert_condition($this->condition_code);
 }
 
 public function get_wind_direction () {
  $wind_directions = array ('северный','северо-восточный','восточный','юго-восточный','южный','юго-западный','западный','северо-западный');
  $a=$this->wind_direction%360;
  if ($a<23 or $a>337) $index=0;
  else if ($a<68) $index=1;
  else if ($a<113) $index=2;
  else if ($a<158) $index=3;
  else if ($a<203) $index=4;
  else if ($a<248) $index=5;
  else if ($a<293) $index=6;
  else if ($a<338) $index=7;
  return $wind_directions[$index];
 }
 
 public function get_visibility () {
  $v=$this->visibility/100;
  if ($v<1) {
   $v*=1000;
   if ($v>1) return $v.' м';
   else return 'нулевая';
  }
  else return $v.' км';
 }
 
 public function correct_minutes($time) {
  return $time+$this->correct_minutes*60;
 }
 public function get_rising () {
  if ($this->rising==1) return 'растёт';
  else if ($this->rising==2) return 'падает';
  else return 'устойчиво';
 }
private function plus_minus($number, $time)
 {
	 if($number<0){
		return $time - (($number*-1)*60*60); 
	 }elseif ($number==0){
		return $time; 
		} else{
		return $time + ($number*60*60);	
		}
 }
}
?>