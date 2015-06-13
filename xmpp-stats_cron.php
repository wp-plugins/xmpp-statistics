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

//Activation hook
function xmpp_stats_activated() {
	//Add statistics cron job
	if(get_option('xmpp_stats_save_data')) wp_schedule_event(time(), 'xmpp_stats_schedule', 'xmpp_stats_cron_job');
	//Create table for statistics
	global $wpdb;
	$table_name = $wpdb->prefix . 'xmpp_stats';
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		type tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
		value int(12) UNSIGNED NOT NULL DEFAULT 0,
		UNIQUE KEY id (id)
	) $charset_collate;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}
register_activation_hook(dirname(__FILE__).'/xmpp-stats.php', 'xmpp_stats_activated');

//Deactivation hook
function xmpp_stats_deactivated() {
	//Remove statistics cron job
	if(get_option('xmpp_stats_save_data')) wp_clear_scheduled_hook('xmpp_stats_cron_job');
}
register_deactivation_hook(dirname(__FILE__).'/xmpp-stats.php', 'xmpp_stats_deactivated' );

//Add cron schedule
function xmpp_stats_schedule($schedules) {
	$schedules['xmpp_stats_schedule'] = array(
		'interval' => 300,
		'display' => __('Every 5 minutes', 'xmpp_stats')
	);
	return $schedules;
}
add_filter('cron_schedules', 'xmpp_stats_schedule');

//Add statistics cron job action
function xmpp_stats_cron_job() {
	//Get current time in UTC
	$now = current_time('mysql', 1);
	//Get statistics
	$online = xmpp_stats_post_xmpp_data('stats onlineusers');
	$registered = xmpp_stats_post_xmpp_data('stats registeredusers');
	$s2s_out = xmpp_stats_post_xmpp_data('getstatsdx s2sconnections');
	$s2s_in = xmpp_stats_post_xmpp_data('getstatsdx s2sservers');
	$xmpp_uptime = xmpp_stats_post_xmpp_data('stats uptimeseconds');
	$system_uptime = xmpp_stats_get_system_data();
	if($system_uptime != 0) $system_uptime = strtotime($now)-$system_uptime;
	//Save statistics to database
	global $wpdb;
	$table_name = $wpdb->prefix . 'xmpp_stats';
	$wpdb->insert(
		$table_name,
		array(
			'timestamp' => $now,
			'type' => '1',
			'value' => $online
		)
	);
	$wpdb->insert(
		$table_name,
		array(
			'timestamp' => $now,
			'type' => '2',
			'value' => $registered
		)
	);
	$wpdb->insert(
		$table_name,
		array(
			'timestamp' => $now,
			'type' => '3',
			'value' => $s2s_out
		)
	);
	$wpdb->insert(
		$table_name,
		array(
			'timestamp' => $now,
			'type' => '4',
			'value' => $s2s_in
		)
	);
	$wpdb->insert(
		$table_name,
		array(
			'timestamp' => $now,
			'type' => '5',
			'value' => $xmpp_uptime
		)
	);
	$wpdb->insert(
		$table_name,
		array(
			'timestamp' => $now,
			'type' => '6',
			'value' => $system_uptime
		)
	);
}
add_action('xmpp_stats_cron_job', 'xmpp_stats_cron_job');
