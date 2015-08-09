<?php

/**
 * contact install
 *
 * @since 1.2.1
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Modules
 * @author edmonsur
 */

function YahooWeather_install()
{
	/** Create yahooweather tabelle  */
			$query = file_get_contents('modules/YahooWeather/database/mysql/create/create_YahooWeather.mysql');
			if ($query)
			{
				if ($this->_config->get('dbPrefix'))
				{
					$query = str_replace($this->_prefixPlaceholder, $this->_config->get('dbPrefix'), $query);
				}
	Redaxscript\Db::rawExecute($query);
			}
	/** Create yahooweather tabelle  */
	
	Redaxscript\Db::forTablePrefix('modules')
		->create()
		->set(array(
			'name' => 'Yahoo Weather',
			'alias' => 'YahooWeather',
			'author' => 'edmonsur',
			'description' => 'Yahoo Weather page',
			'version' => '2.4.0'
		))
		->save();

		
		
		
		}

/**
 * contact uninstall
 *
 * @since 1.2.1
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Modules
 * @author  edmonsur
 */

function YahooWeather_uninstall()
{
	Redaxscript\Db::forTablePrefix('modules')->where('alias', 'YahooWeather')->findMany()->delete();

		/** Drop yahooweather tabelle  */
			$query = file_get_contents('modules/YahooWeather/database/mysql/drop/drop_YahooWeather.mysql');
			if ($query)
			{
				if ($this->_config->get('dbPrefix'))
				{
					$query = str_replace($this->_prefixPlaceholder, $this->_config->get('dbPrefix'), $query);
				}
	Redaxscript\Db::rawExecute($query);
			}
	/** Drop yahooweather tabelle  */
}

