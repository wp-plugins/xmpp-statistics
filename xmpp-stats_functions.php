<?php
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

//Get XMPP data by REST
function xmpp_stats_post_xmpp_data($data) {
	//Authorization
	$auth = get_option('xmpp_stats_auth');
	if($auth) {
		$login = str_replace('@', '" "', get_option('xmpp_stats_login'));
		$password = get_option('xmpp_stats_password');
		$auth_data = '--auth "'.$login.'" "'.$password.'" ';
		$data = $auth_data.$data;
	}
	//POST data
	$args = array(
		'body' => $data,
		'timeout' => 5,
		'redirection' => 0,
		'sslverify' => false
	);
	//Get data
	$rest_url = get_option('xmpp_stats_rest_url');
	$response = wp_remote_post($rest_url, $args);
	$http_code = wp_remote_retrieve_response_code($response);
	//Verify response
	if($http_code == 200) {
		//Set last activity information
		if(($auth)&&(get_option('xmpp_stats_set_last'))) {
			//Get current time in UTC
			$now = current_time('timestamp', 1);
			//POST data
			$args = array(
				'body' => $auth_data.'set_last "'.$login.'" "'.$now.'" "Set by XMPP Statistics"',
				'timeout' => 5,
				'redirection' => 0,
				'sslverify' => false
			);
			//Send command
			wp_remote_post($rest_url, $args);
		}
		//Return data
		return wp_remote_retrieve_body($response);
	}
	//No data
	return null;
}

//Get system data by HTTP
function xmpp_stats_get_system_data() {
	//POST data
	$args = array(
		'timeout' => 5,
		'redirection' => 0,
		'sslverify' => false
	);
	//Get data
	$response = wp_remote_get(get_option('xmpp_stats_uptime_url'), $args);
	$http_code = wp_remote_retrieve_response_code($response);
	//Verify response
	if($http_code == 200) {
		return wp_remote_retrieve_body($response);
	}
	//No data
	return null;
}

//Change seconds to friendly view
function xmpp_stats_seconds_to_datestamp($seconds)
{
	if($seconds == 0) return '0s';
    $can_print = false;
    $divs = array(86400, 3600, 60, 1);
    for($div=0; $div<4; $div++) {
        $res = (int)($seconds/$divs[$div]);
        $rem = $seconds % $divs[$div];
        if($res != 0) $can_print = true;
        if($can_print) $return .= sprintf('%d%s ', $res, substr('dhms', $div, 1));
        $seconds = $rem;
    }
    return trim($return);
}

//Change second to friendly view #2
function xmpp_stats_timestamp_to_date($timestamp) {
	$now = current_time('timestamp');
	$last_midnight = $now - ($now % (24*60*60));
	$day_name = date_i18n("j M, G:i T", $timestamp);
	if($timestamp >= $last_midnight) {
		$day_name = __('today', 'xmpp_stats').', '.date_i18n("G:i T", $timestamp);
	} else if($timestamp >= ($last_midnight-(24*60*60))) {
		$day_name = __('yesterday', 'xmpp_stats').', '.date_i18n("G:i T", $timestamp);
	}
	return $day_name;
}
