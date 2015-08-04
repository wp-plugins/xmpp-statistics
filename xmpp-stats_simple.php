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

//Enqueue shortcodes styles & jQuery scripts
function xmpp_stats_enqueue_shortcodes_scripts() {
	global $post;

	if(is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'xmpp_onlineusers')) {
		wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.5', 'all');
		wp_enqueue_style('fontawesome', plugin_dir_url(__FILE__).'css/font-awesome.min.css', array(), '4.4.0', 'all');
		wp_enqueue_script('xmpp-onlineusers', plugin_dir_url(__FILE__).'js/jquery.xmpp-onlineusers.js', array('jquery'), XMPP_STATS_VERSION, true);
		wp_localize_script('xmpp-onlineusers', 'xmpp_onlineusers', array(
			'url' => admin_url('admin-ajax.php?action=get_xmpp_onlineusers&lang='.get_locale())
		));
	}
	if(is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'xmpp_registeredusers')) {
		wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.5', 'all');
		wp_enqueue_style('fontawesome', plugin_dir_url(__FILE__).'css/font-awesome.min.css', array(), '4.4.0', 'all');
		wp_enqueue_script('xmpp-registeredusers', plugin_dir_url(__FILE__).'js/jquery.xmpp-registeredusers.js', array('jquery'), XMPP_STATS_VERSION, true);
		wp_localize_script('xmpp-registeredusers', 'xmpp_registeredusers', array(
			'url' => admin_url('admin-ajax.php?action=get_xmpp_registeredusers&lang='.get_locale())
		));
	}
	if(is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'xmpp_s2s_out')) {
		wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.5', 'all');
		wp_enqueue_style('fontawesome', plugin_dir_url(__FILE__).'css/font-awesome.min.css', array(), '4.4.0', 'all');
		wp_enqueue_script('xmpp-s2s-out', plugin_dir_url(__FILE__).'js/jquery.xmpp-s2s-out.js', array('jquery'), XMPP_STATS_VERSION, true);
		wp_localize_script('xmpp-s2s-out', 'xmpp_s2s_out', array(
			'url' => admin_url('admin-ajax.php?action=get_xmpp_s2s_out&lang='.get_locale())
		));
	}
	if(is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'xmpp_s2s_in')) {
		wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.5', 'all');
		wp_enqueue_style('fontawesome', plugin_dir_url(__FILE__).'css/font-awesome.min.css', array(), '4.4.0', 'all');
		wp_enqueue_script('xmpp-s2s-in', plugin_dir_url(__FILE__).'js/jquery.xmpp-s2s-in.js', array('jquery'), XMPP_STATS_VERSION, true);
		wp_localize_script('xmpp-s2s-in', 'xmpp_s2s_in', array(
			'url' => admin_url('admin-ajax.php?action=get_xmpp_s2s_in&lang='.get_locale())
		));
	}
	if(is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'xmpp_uptime')) {
		wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.5', 'all');
		wp_enqueue_style('fontawesome', plugin_dir_url(__FILE__).'css/font-awesome.min.css', array(), '4.4.0', 'all');
		wp_enqueue_script('xmpp-uptime', plugin_dir_url(__FILE__).'js/jquery.xmpp-uptime.js', array('jquery'), XMPP_STATS_VERSION, true);
		wp_localize_script('xmpp-uptime', 'xmpp_uptime', array(
			'url' => admin_url('admin-ajax.php?action=get_xmpp_uptime&lang='.get_locale())
		));
	}
	if(is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'system_uptime')) {
		wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.5', 'all');
		wp_enqueue_style('fontawesome', plugin_dir_url(__FILE__).'css/font-awesome.min.css', array(), '4.4.0', 'all');
		wp_enqueue_script('system-uptime', plugin_dir_url(__FILE__).'js/jquery.system-uptime.js', array('jquery'), XMPP_STATS_VERSION, true);
		wp_localize_script('system-uptime', 'system_uptime', array(
			'url' => admin_url('admin-ajax.php?action=get_system_uptime&lang='.get_locale())
		));
	}
}
add_action('wp_enqueue_scripts', 'xmpp_stats_enqueue_shortcodes_scripts');

//Get online users count
function shortcode_xmpp_onlineusers($attr) {
	//Return loading information
	return '<div id="xmpp_onlineusers"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-pulse"></i></div>';
}
//Enqueue ajax function
function shortcode_xmpp_onlineusers_ajax() {
	$data = xmpp_stats_post_xmpp_data('stats onlineusers');
	if(is_null($data)) echo '-';
	echo $data;
	exit;
}
add_action('wp_ajax_nopriv_get_xmpp_onlineusers', 'shortcode_xmpp_onlineusers_ajax');
add_action('wp_ajax_get_xmpp_onlineusers', 'shortcode_xmpp_onlineusers_ajax');

//Get registered users count
function shortcode_xmpp_registeredusers($attr) {
	//Return loading information
	return '<div id="xmpp_registeredusers"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-pulse"></i></div>';
}
//Enqueue ajax function
function shortcode_xmpp_registeredusers_ajax() {
	$data = xmpp_stats_post_xmpp_data('stats registeredusers');
	if(is_null($data)) $data = '-';
	echo $data;
	exit;
}
add_action('wp_ajax_nopriv_get_xmpp_registeredusers', 'shortcode_xmpp_registeredusers_ajax');
add_action('wp_ajax_get_xmpp_registeredusers', 'shortcode_xmpp_registeredusers_ajax');

//Get outgoing s2s connections count
function shortcode_xmpp_s2s_out($attr) {
	//Return loading information
	return '<div id="xmpp_s2s_out"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-pulse"></i></div>';
}
//Enqueue ajax function
function shortcode_xmpp_s2s_out_ajax() {
	$data = xmpp_stats_post_xmpp_data('getstatsdx s2sconnections');
	if(is_null($data)) echo '-';
	echo $data;
	exit;
}
add_action('wp_ajax_nopriv_get_xmpp_s2s_out', 'shortcode_xmpp_s2s_out_ajax');
add_action('wp_ajax_get_xmpp_s2s_out', 'shortcode_xmpp_s2s_out_ajax');

//Get incoming s2s connections count
function shortcode_xmpp_s2s_in($attr) {
	//Return loading information
	return '<div id="xmpp_s2s_in"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-pulse"></i></div>';
}
//Enqueue ajax function
function shortcode_xmpp_s2s_in_ajax() {
	$data = xmpp_stats_post_xmpp_data('getstatsdx s2sservers');
	if(is_null($data)) echo '-';
	echo $data;
	exit;
}
add_action('wp_ajax_nopriv_get_xmpp_s2s_in', 'shortcode_xmpp_s2s_in_ajax');
add_action('wp_ajax_get_xmpp_s2s_in', 'shortcode_xmpp_s2s_in_ajax');

//Get XMPP uptime
function shortcode_xmpp_uptime($attr) {
	//Return loading information
	return '<div id="xmpp_uptime"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-pulse"></i></div>';
}
//Enqueue ajax function
function shortcode_xmpp_uptime_ajax() {
	$data = xmpp_stats_post_xmpp_data('stats uptimeseconds');
	if(is_null($data)) echo '-';
	else {
		$last_restart = __('Last restart', 'xmpp_stats').' '.xmpp_stats_timestamp_to_date(current_time('timestamp')-$data);
		echo '<span class="href hint--left hint--info" data-hint="'.$last_restart.'">'.xmpp_stats_seconds_to_datestamp($data).'</span>';
	}
	exit;
}
add_action('wp_ajax_nopriv_get_xmpp_uptime', 'shortcode_xmpp_uptime_ajax');
add_action('wp_ajax_get_xmpp_uptime', 'shortcode_xmpp_uptime_ajax');

//Get system uptime
function shortcode_system_uptime($attr) {
	//Return loading information
	return '<div id="system_uptime"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-pulse"></i></div>';
}
//Enqueue ajax function
function shortcode_system_uptime_ajax() {
	$data = xmpp_stats_get_system_data();
	if(is_null($data)) echo '-';
	else {
		$last_restart = __('Last restart', 'xmpp_stats').' '.xmpp_stats_timestamp_to_date($data+(wp_timezone_override_offset()*3600));
		echo '<span class="href hint--left hint--info" data-hint="'.$last_restart.'">'.xmpp_stats_seconds_to_datestamp(current_time('timestamp')-$data-(wp_timezone_override_offset()*3600)).'</span>';
	}
	exit;
}
add_action('wp_ajax_nopriv_get_system_uptime', 'shortcode_system_uptime_ajax');
add_action('wp_ajax_get_system_uptime', 'shortcode_system_uptime_ajax');

//Add shortcodes
add_shortcode('xmpp_onlineusers', 'shortcode_xmpp_onlineusers');
add_shortcode('xmpp_registeredusers', 'shortcode_xmpp_registeredusers');
add_shortcode('xmpp_s2s_out', 'shortcode_xmpp_s2s_out');
add_shortcode('xmpp_s2s_in', 'shortcode_xmpp_s2s_in');
add_shortcode('xmpp_uptime', 'shortcode_xmpp_uptime');
add_shortcode('system_uptime', 'shortcode_system_uptime');
