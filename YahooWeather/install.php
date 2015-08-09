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


function YahooWeather_uninstall()
{
	Redaxscript\Db::forTablePrefix('modules')->where('alias', 'YahooWeather')->findMany()->delete();
}

