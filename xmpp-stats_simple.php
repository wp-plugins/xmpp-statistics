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

//Enqueue shortcodes styles
function xmpp_stats_enqueue_shortcodes_styles() {
	wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.3', 'all');
	wp_enqueue_style('fontawesome', plugin_dir_url(__FILE__).'css/font-awesome.min.css', array(), '4.3.0', 'all');
}
add_action('wp_enqueue_scripts', 'xmpp_stats_enqueue_shortcodes_styles');

//Get online users count
function shortcode_xmpp_onlineusers($attr) {
	//Enqueue jQuery script
	wp_enqueue_script('xmpp-onlineusers', admin_url('admin-ajax.php?action=xmpp_onlineusers&lang='.get_locale()), array(), XMPP_STATS_VERSION, true);
	//Return loading information
	return '<div id="xmpp_onlineusers"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
//Enqueue jQuery script via ajax
function shortcode_xmpp_onlineusers_jquery() {
	header("content-type: text/javascript; charset=UTF-8"); ?>
	jQuery(document).ready(function($) {
		function ajax_get_xmpp_onlineusers()
		{
			$.post('<?php echo admin_url('admin-ajax.php?action=get_xmpp_onlineusers&lang='.get_locale()); ?>', function(response) {
				$('#xmpp_onlineusers').html(response);
			});
		}
		ajax_get_xmpp_onlineusers();
		setInterval(function() {
			ajax_get_xmpp_onlineusers();
		}, 60000);
	}); <?
	exit;
}
add_action('wp_ajax_nopriv_xmpp_onlineusers', 'shortcode_xmpp_onlineusers_jquery');
add_action('wp_ajax_xmpp_onlineusers', 'shortcode_xmpp_onlineusers_jquery');
//Enqueue ajax function
function shortcode_xmpp_onlineusers_ajax() {
	echo xmpp_stats_get_xmpp_data('stats onlineusers');
	exit;
}
add_action('wp_ajax_nopriv_get_xmpp_onlineusers', 'shortcode_xmpp_onlineusers_ajax');
add_action('wp_ajax_get_xmpp_onlineusers', 'shortcode_xmpp_onlineusers_ajax');

//Get registered users count
function shortcode_xmpp_registeredusers($attr) {
	//Enqueue jQuery script
	wp_enqueue_script('xmpp-registeredusers', admin_url('admin-ajax.php?action=xmpp_registeredusers&lang='.get_locale()), array(), XMPP_STATS_VERSION, true);
	//Return loading information
	return '<div id="xmpp_registeredusers"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
//Enqueue jQuery script via ajax
function shortcode_xmpp_registeredusers_jquery() {
	header("content-type: text/javascript; charset=UTF-8"); ?>
	jQuery(document).ready(function($) {
		function ajax_get_xmpp_registeredusers()
		{
			$.post('<?php echo admin_url('admin-ajax.php?action=get_xmpp_registeredusers&lang='.get_locale()); ?>', function(response) {
				$('#xmpp_registeredusers').html(response);
			});
		}
		ajax_get_xmpp_registeredusers();
		setInterval(function() {
			ajax_get_xmpp_registeredusers();
		}, 60000);
	}); <?
	exit;
}
add_action('wp_ajax_nopriv_xmpp_registeredusers', 'shortcode_xmpp_registeredusers_jquery');
add_action('wp_ajax_xmpp_registeredusers', 'shortcode_xmpp_registeredusers_jquery');
//Enqueue ajax function
function shortcode_xmpp_registeredusers_ajax() {
	echo xmpp_stats_get_xmpp_data('stats registeredusers');
	exit;
}
add_action('wp_ajax_nopriv_get_xmpp_registeredusers', 'shortcode_xmpp_registeredusers_ajax');
add_action('wp_ajax_get_xmpp_registeredusers', 'shortcode_xmpp_registeredusers_ajax');

//Get outgoing s2s connections count
function shortcode_xmpp_s2s_out($attr) {
	//Enqueue jQuery script
	wp_enqueue_script('xmpp-s2s_out', admin_url('admin-ajax.php?action=xmpp_s2s_out&lang='.get_locale()), array(), XMPP_STATS_VERSION, true);
	//Return loading information
	return '<div id="xmpp_s2s_out"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
//Enqueue jQuery script via ajax
function shortcode_xmpp_s2s_out_jquery() {
	header("content-type: text/javascript; charset=UTF-8"); ?>
	jQuery(document).ready(function($) {
		function ajax_get_xmpp_s2s_out()
		{
			$.post('<?php echo admin_url('admin-ajax.php?action=get_xmpp_s2s_out&lang='.get_locale()); ?>', function(response) {
				$('#xmpp_s2s_out').html(response);
			});
		}
		ajax_get_xmpp_s2s_out();
		setInterval(function() {
			ajax_get_xmpp_s2s_out();
		}, 60000);
	}); <?
	exit;
}
add_action('wp_ajax_nopriv_xmpp_s2s_out', 'shortcode_xmpp_s2s_out_jquery');
add_action('wp_ajax_xmpp_s2s_out', 'shortcode_xmpp_s2s_out_jquery');
//Enqueue ajax function
function shortcode_xmpp_s2s_out_ajax() {
	echo xmpp_stats_get_xmpp_data('getstatsdx s2sconnections');
	exit;
}
add_action('wp_ajax_nopriv_get_xmpp_s2s_out', 'shortcode_xmpp_s2s_out_ajax');
add_action('wp_ajax_get_xmpp_s2s_out', 'shortcode_xmpp_s2s_out_ajax');

//Get incoming s2s connections count
function shortcode_xmpp_s2s_in($attr) {
	//Enqueue jQuery script
	wp_enqueue_script('xmpp-s2s_in', admin_url('admin-ajax.php?action=xmpp_s2s_in&lang='.get_locale()), array(), XMPP_STATS_VERSION, true);
	//Return loading information
	return '<div id="xmpp_s2s_in"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
//Enqueue jQuery script via ajax
function shortcode_xmpp_s2s_in_jquery() {
	header("content-type: text/javascript; charset=UTF-8"); ?>
	jQuery(document).ready(function($) {
		function ajax_get_xmpp_s2s_in()
		{
			$.post('<?php echo admin_url('admin-ajax.php?action=get_xmpp_s2s_in&lang='.get_locale()); ?>', function(response) {
				$('#xmpp_s2s_in').html(response);
			});
		}
		ajax_get_xmpp_s2s_in();
		setInterval(function() {
			ajax_get_xmpp_s2s_in();
		}, 60000);
	}); <?
	exit;
}
add_action('wp_ajax_nopriv_xmpp_s2s_in', 'shortcode_xmpp_s2s_in_jquery');
add_action('wp_ajax_xmpp_s2s_in', 'shortcode_xmpp_s2s_in_jquery');
//Enqueue ajax function
function shortcode_xmpp_s2s_in_ajax() {
	echo xmpp_stats_get_xmpp_data('getstatsdx s2sservers');
	exit;
}
add_action('wp_ajax_nopriv_get_xmpp_s2s_in', 'shortcode_xmpp_s2s_in_ajax');
add_action('wp_ajax_get_xmpp_s2s_in', 'shortcode_xmpp_s2s_in_ajax');

//Get XMPP uptime
function shortcode_xmpp_uptime($attr) {
	//Enqueue jQuery script
	wp_enqueue_script('xmpp-uptime', admin_url('admin-ajax.php?action=xmpp_uptime&lang='.get_locale()), array(), XMPP_STATS_VERSION, true);
	//Return loading information
	return '<div id="xmpp_uptime"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
//Enqueue jQuery script via ajax
function shortcode_xmpp_uptime_jquery() {
	header("content-type: text/javascript; charset=UTF-8"); ?>
	jQuery(document).ready(function($) {
		function ajax_get_xmpp_uptime()
		{
			$.post('<?php echo admin_url('admin-ajax.php?action=get_xmpp_uptime&lang='.get_locale()); ?>', function(response) {
				$('#xmpp_uptime').html(response);
			});
		}
		ajax_get_xmpp_uptime();
		setInterval(function() {
			ajax_get_xmpp_uptime();
		}, 60000);
	}); <?
	exit;
}
add_action('wp_ajax_nopriv_xmpp_uptime', 'shortcode_xmpp_uptime_jquery');
add_action('wp_ajax_xmpp_uptime', 'shortcode_xmpp_uptime_jquery');
//Enqueue ajax function
function shortcode_xmpp_uptime_ajax() {
	$seconds = xmpp_stats_get_xmpp_data('stats uptimeseconds');
	if($seconds=='-') echo '-';
	else {
		$last_restart = __('Last restart', 'xmpp_stats').' '.xmpp_stats_timestamp_to_date(current_time('timestamp')-$seconds);
		echo '<span class="href hint--left hint--success" data-hint="'.$last_restart.'">'.xmpp_stats_seconds_to_datestamp($seconds).'</span>';
	}
	exit;
}
add_action('wp_ajax_nopriv_get_xmpp_uptime', 'shortcode_xmpp_uptime_ajax');
add_action('wp_ajax_get_xmpp_uptime', 'shortcode_xmpp_uptime_ajax');

//Get system uptime
function shortcode_system_uptime($attr) {
	//Enqueue jQuery script
	wp_enqueue_script('system-uptime', admin_url('admin-ajax.php?action=system_uptime&lang='.get_locale()), array(), XMPP_STATS_VERSION, true);
	//Return loading information
	return '<div id="system_uptime"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
//Enqueue jQuery script via ajax
function shortcode_system_uptime_jquery() {
	header("content-type: text/javascript; charset=UTF-8"); ?>
	jQuery(document).ready(function($) {
		function ajax_get_system_uptime()
		{
			$.post('<?php echo admin_url('admin-ajax.php?action=get_system_uptime&lang='.get_locale()); ?>', function(response) {
				$('#system_uptime').html(response);
			});
		}
		ajax_get_system_uptime();
		setInterval(function() {
			ajax_get_system_uptime();
		}, 60000);
	}); <?
	exit;
}
add_action('wp_ajax_nopriv_system_uptime', 'shortcode_system_uptime_jquery');
add_action('wp_ajax_system_uptime', 'shortcode_system_uptime_jquery');
//Enqueue ajax function
function shortcode_system_uptime_ajax() {
	$timestamp = xmpp_stats_get_system_data();
	if($timestamp=='-') echo '-';
	else {
		$last_restart = __('Last restart', 'xmpp_stats').' '.xmpp_stats_timestamp_to_date($timestamp+(wp_timezone_override_offset()*3600));
		echo '<span class="href hint--left hint--success" data-hint="'.$last_restart.'">'.xmpp_stats_seconds_to_datestamp(current_time('timestamp')-$timestamp-(wp_timezone_override_offset()*3600)).'</span>';
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
