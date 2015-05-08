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

//Add shortcodes
add_shortcode('xmpp_onlineusers', 'shortcode_xmpp_onlineusers');
add_shortcode('xmpp_registeredusers', 'shortcode_xmpp_registeredusers');
add_shortcode('xmpp_s2s_out', 'shortcode_xmpp_s2s_out');
add_shortcode('xmpp_s2s_in', 'shortcode_xmpp_s2s_in');
add_shortcode('xmpp_uptime', 'shortcode_xmpp_uptime');
add_shortcode('system_uptime', 'shortcode_system_uptime');

//Get online users count
function shortcode_xmpp_onlineusers($attr) {
	//Add styles
	wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.3', false);
	//Add jQuery script
	add_action('wp_footer', 'shortcode_xmpp_onlineusers_jquery');
	function shortcode_xmpp_onlineusers_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			function ajax_get_xmpp_onlineusers()
			{
				var data = {
					'action': 'get_xmpp_onlineusers'
				};
				$.post('<?php echo admin_url('admin-ajax.php?lang='.get_locale()); ?>', data, function(response) {
					$('#xmpp_onlineusers').html(response);
				});
			}
			ajax_get_xmpp_onlineusers();
			setInterval(function() {
				ajax_get_xmpp_onlineusers();
			}, 30000);
		});
		</script>
	<?php }
	return '<div id="xmpp_onlineusers"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
add_action('wp_ajax_nopriv_get_xmpp_onlineusers', 'shortcode_xmpp_onlineusers_ajax');
add_action('wp_ajax_get_xmpp_onlineusers', 'shortcode_xmpp_onlineusers_ajax');
function shortcode_xmpp_onlineusers_ajax() {
	echo xmpp_stats_get_xmpp_data('stats onlineusers');
	die();
}

//Get registered users count
function shortcode_xmpp_registeredusers($attr) {
	//Add styles
	wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.3', false);
	//Add jQuery script
	add_action('wp_footer', 'shortcode_xmpp_registeredusers_jquery');
	function shortcode_xmpp_registeredusers_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			function ajax_get_xmpp_registeredusers()
			{
				var data = {
					'action': 'get_xmpp_registeredusers'
				};
				$.post('<?php echo admin_url('admin-ajax.php?lang='.get_locale()); ?>', data, function(response) {
					$('#xmpp_registeredusers').html(response);
				});
			}
			ajax_get_xmpp_registeredusers();
			setInterval(function() {
				ajax_get_xmpp_registeredusers();
			}, 30000);
		});
		</script>
	<?php }
	return '<div id="xmpp_registeredusers"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
add_action('wp_ajax_nopriv_get_xmpp_registeredusers', 'shortcode_xmpp_registeredusers_ajax');
add_action('wp_ajax_get_xmpp_registeredusers', 'shortcode_xmpp_registeredusers_ajax');
function shortcode_xmpp_registeredusers_ajax() {
	echo xmpp_stats_get_xmpp_data('stats registeredusers');
	die();
}

//Get outgoing s2s connections count
function shortcode_xmpp_s2s_out($attr) {
	//Add styles
	wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.3', false);
	//Add jQuery script
	add_action('wp_footer', 'shortcode_xmpp_s2s_out_jquery');
	function shortcode_xmpp_s2s_out_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			function ajax_get_xmpp_s2s_out()
			{
				var data = {
					'action': 'get_xmpp_s2s_out'
				};
				$.post('<?php echo admin_url('admin-ajax.php?lang='.get_locale()); ?>', data, function(response) {
					$('#xmpp_s2s_out').html(response);
				});
			}
			ajax_get_xmpp_s2s_out();
			setInterval(function() {
				ajax_get_xmpp_s2s_out();
			}, 30000);
		});
		</script>
	<?php }
	return '<div id="xmpp_s2s_out"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
add_action('wp_ajax_nopriv_get_xmpp_s2s_out', 'shortcode_xmpp_s2s_out_ajax');
add_action('wp_ajax_get_xmpp_s2s_out', 'shortcode_xmpp_s2s_out_ajax');
function shortcode_xmpp_s2s_out_ajax() {
	echo xmpp_stats_get_xmpp_data('getstatsdx s2sconnections');
	die();
}

//Get incoming s2s connections count
function shortcode_xmpp_s2s_in($attr) {
	//Add styles
	wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.3', false);
	//Add jQuery script
	add_action('wp_footer', 'shortcode_xmpp_s2s_in_jquery');
	function shortcode_xmpp_s2s_in_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			function ajax_get_xmpp_s2s_in()
			{
				var data = {
					'action': 'get_xmpp_s2s_in'
				};
				$.post('<?php echo admin_url('admin-ajax.php?lang='.get_locale()); ?>', data, function(response) {
					$('#xmpp_s2s_in').html(response);
				});
			}
			ajax_get_xmpp_s2s_in();
			setInterval(function() {
				ajax_get_xmpp_s2s_in();
			}, 30000);
		});
		</script>
	<?php }
	return '<div id="xmpp_s2s_in"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
add_action('wp_ajax_nopriv_get_xmpp_s2s_in', 'shortcode_xmpp_s2s_in_ajax');
add_action('wp_ajax_get_xmpp_s2s_in', 'shortcode_xmpp_s2s_in_ajax');
function shortcode_xmpp_s2s_in_ajax() {
	echo xmpp_stats_get_xmpp_data('getstatsdx s2sservers');
	die();
}

//Get XMPP uptime
function shortcode_xmpp_uptime($attr) {
	//Add styles
	wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.3', false);
	//Add jQuery script
	add_action('wp_footer', 'shortcode_xmpp_uptime_jquery');
	function shortcode_xmpp_uptime_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			function ajax_get_xmpp_uptime()
			{
				var data = {
					'action': 'get_xmpp_uptime'
				};
				$.post('<?php echo admin_url('admin-ajax.php?lang='.get_locale()); ?>', data, function(response) {
					$('#xmpp_uptime').html(response);
				});
			}
			ajax_get_xmpp_uptime();
			setInterval(function() {
				ajax_get_xmpp_uptime();
			}, 30000);
		});
		</script>
	<?php }
	return '<div id="xmpp_uptime"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
add_action('wp_ajax_nopriv_get_xmpp_uptime', 'shortcode_xmpp_uptime_ajax');
add_action('wp_ajax_get_xmpp_uptime', 'shortcode_xmpp_uptime_ajax');
function shortcode_xmpp_uptime_ajax() {
	$seconds = xmpp_stats_get_xmpp_data('stats uptimeseconds');
	if($seconds=='-') echo '-';
	else {
		$last_restart = __('Last restart', 'xmpp_stats').' '.xmpp_stats_timestamp_to_date(current_time('timestamp')-$seconds);
		echo '<span class="href hint--left hint--success" data-hint="'.$last_restart.'">'.xmpp_stats_seconds_to_datestamp($seconds).'</span>';
	}
	die();
}

//Get system uptime
function shortcode_system_uptime($attr) {
	//Add styles
	wp_enqueue_style('hint', plugin_dir_url(__FILE__).'css/hint.min.css', array(), '1.3.3', false);
	//Add jQuery script
	add_action('wp_footer', 'shortcode_system_uptime_jquery');
	function shortcode_system_uptime_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			function ajax_get_system_uptime()
			{
				var data = {
					'action': 'get_system_uptime'
				};
				$.post('<?php echo admin_url('admin-ajax.php?lang='.get_locale()); ?>', data, function(response) {
					$('#system_uptime').html(response);
				});
			}
			ajax_get_system_uptime();
			setInterval(function() {
				ajax_get_system_uptime();
			}, 30000);
		});
		</script>
	<?php }
	return '<div id="system_uptime"><i title="'.__('Loading...', 'xmpp_stats').'" class="fa fa-spinner fa-spin"></i></div>';
}
add_action('wp_ajax_nopriv_get_system_uptime', 'shortcode_system_uptime_ajax');
add_action('wp_ajax_get_system_uptime', 'shortcode_system_uptime_ajax');
function shortcode_system_uptime_ajax() {
	$timestamp = xmpp_stats_get_system_data();
	if($timestamp=='-') echo '-';
	else {
		$last_restart = __('Last restart', 'xmpp_stats').' '.xmpp_stats_timestamp_to_date($timestamp+(wp_timezone_override_offset()*3600));
		echo '<span class="href hint--left hint--success" data-hint="'.$last_restart.'">'.xmpp_stats_seconds_to_datestamp(current_time('timestamp')-$timestamp-(wp_timezone_override_offset()*3600)).'</span>';
	}
	die();
}
