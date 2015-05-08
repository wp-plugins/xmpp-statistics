<?php
/*
Plugin Name: XMPP Statistics
Plugin URI: http://beherit.pl/en/wordpress/plugins/xmpp-statistics
Description: Display the statistics from ejabberd XMPP server.
Version: 1.0
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

//Define plugin basename
define('PLUGIN_BASENAME', plugin_basename(__FILE__));

//Define translations
add_action('init', 'xmpp_stats_textdomain');
function xmpp_stats_textdomain() {
	load_plugin_textdomain('xmpp_stats', false, dirname(plugin_basename(__FILE__)).'/languages');
}

//Localization filter (Ajax bugfix)
add_filter('locale', 'xmpp_stats_localization_filter', 99);
function xmpp_stats_localization_filter($locale) {
	if(!empty($_GET['lang']))
		return $_GET['lang'];
	return $locale;
}

//Include admin settings
include dirname(__FILE__).'/xmpp-stats_admin.php';

//Include cron
include dirname(__FILE__).'/xmpp-stats_cron.php';

//Include simple stats
include dirname(__FILE__).'/xmpp-stats_simple.php';

//Include graphs
include dirname(__FILE__).'/xmpp-stats_graphs.php';

//Include functions
include dirname(__FILE__).'/xmpp-stats_functions.php';
