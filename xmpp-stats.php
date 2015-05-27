<?php
/*
Plugin Name: XMPP Statistics
Plugin URI: http://beherit.pl/en/wordpress/plugins/xmpp-statistics
Description: Display the statistics from ejabberd XMPP server.
Version: 1.2
Author: Krzysztof Grochocki
Author URI: http://beherit.pl/
License: GPLv3
*/

/*
	Copyright (C) 2015 Krzysztof Grochocki
	
	This file is part of XMPP Statistics.
	
	XMPP Statistics is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3, or
	(at your option) any later version.
	
	XMPP Statistics is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with GNU Radio. If not, see <http://www.gnu.org/licenses/>.
*/

//Define plugin version variable
define('XMPP_STATS_VERSION', '1.2');

//Define translations
function xmpp_stats_textdomain() {
	load_plugin_textdomain('xmpp_stats', false, dirname(plugin_basename(__FILE__)).'/languages');
}
add_action('init', 'xmpp_stats_textdomain');

//Localization filter (Ajax bugfix)
function xmpp_stats_localization_filter($locale) {
	if(!empty($_GET['lang']))
		return $_GET['lang'];
	return $locale;
}
add_filter('locale', 'xmpp_stats_localization_filter', 99);

//Include admin settings
include_once dirname(__FILE__).'/xmpp-stats_admin.php';

//Include functions
include_once dirname(__FILE__).'/xmpp-stats_functions.php';

//Include cron
include_once dirname(__FILE__).'/xmpp-stats_cron.php';

//Include simple stats
include_once dirname(__FILE__).'/xmpp-stats_simple.php';

//Include graphs
include_once dirname(__FILE__).'/xmpp-stats_graphs.php';
