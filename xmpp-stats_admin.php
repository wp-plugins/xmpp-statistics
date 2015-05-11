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

//Admin init
add_action('admin_init', 'xmpp_stats_register_settings');
function xmpp_stats_register_settings() {
	//Register settings
	register_setting('xmpp_stats_settings', 'xmpp_stats_rest_url');
	register_setting('xmpp_stats_settings', 'xmpp_stats_uptime_url');
	register_setting('xmpp_stats_settings', 'xmpp_stats_save_data');
	register_setting('xmpp_stats_settings', 'xmpp_stats_auth');
	register_setting('xmpp_stats_settings', 'xmpp_stats_login');
	register_setting('xmpp_stats_settings', 'xmpp_stats_password');
	register_setting('xmpp_stats_settings', 'xmpp_stats_set_last');
	//Add row to plugin page
	add_filter('plugin_row_meta', 'xmpp_stats_plugin_row_meta', 10, 2);
}

//Settings row on plugin page
function xmpp_stats_plugin_row_meta($plugin_meta, $plugin_file) {
	if(dirname(plugin_basename(__FILE__)).'/xmpp-stats.php'==$plugin_file)
		$plugin_meta[] = '<a href="options-general.php?page=xmpp-stats-options">'.__('Settings', 'xmpp_stats').'</a>';
    return $plugin_meta;
}

//Create options menu
add_action('admin_menu', 'xmpp_stats_add_admin_menu');
function xmpp_stats_add_admin_menu() {
	//Global variable
	global $xmpp_stats_options_page_hook;
	//Add options page
	$xmpp_stats_options_page_hook = add_options_page(__('XMPP Statistics', 'xmpp_stats'), __('XMPP Statistics', 'xmpp_stats'), 'manage_options', 'xmpp-stats-options', 'xmpp_stats_options');
	//Add the needed JavaScript
	add_action('admin_enqueue_scripts', 'xmpp_stats_options_enqueue_scripts');
	//Add the needed jQuery script
	add_action('admin_footer-'.$xmpp_stats_options_page_hook, 'xmpp_stats_options_scripts' );
	//Set number of available columns
	add_filter('screen_layout_columns', 'xmpp_stats_options_layout_column', 10, 2);
	//Add options page hook
	add_action('load-'.$xmpp_stats_options_page_hook, 'xmpp_stats_options_hook');
}

//Add the needed JavaScript
function xmpp_stats_options_enqueue_scripts($hook_suffix) {
	//Get global variable
	global $xmpp_stats_options_page_hook;
	if($hook_suffix == $xmpp_stats_options_page_hook) {
		wp_enqueue_script('postbox');
	}
}

//Add the needed jQuery script
function xmpp_stats_options_scripts() {
	//Get global variable
	global $xmpp_stats_options_page_hook; ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			//Toggle postbox
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			//Save postbox status
			postboxes.add_postbox_toggles( '<?php echo $xmpp_stats_options_page_hook; ?>' );
		});
		//]]>
	</script>
<?php }

//Number of columns available in options page
function xmpp_stats_options_layout_column($columns, $screen) {
	//Get global variable
	global $xmpp_stats_options_page_hook;
	if($screen == $xmpp_stats_options_page_hook) {
		$columns[$xmpp_stats_options_page_hook] = 2;
	}
	return $columns;
}

//Options page hook
function xmpp_stats_options_hook() {
	if((isset($_GET['settings-updated']))&&($_GET['settings-updated'])) {
		//Check cron job activation
		$cron_job_active = wp_next_scheduled('xmpp_stats_cron_job');
		//Turn on cron job
		if((get_option('xmpp_stats_save_data'))&&(!$cron_job_active)) wp_schedule_event(time(), 'xmpp_stats_schedule', 'xmpp_stats_cron_job');
		//Turn off cron job
		else if((!get_option('xmpp_stats_save_data'))&&($cron_job_active)) wp_clear_scheduled_hook('xmpp_stats_cron_job');
	}
}

//Add metaboxes
add_action('add_meta_boxes', 'xmpp_stats_add_meta_boxes');
function xmpp_stats_add_meta_boxes() {
	//Get global variable
	global $xmpp_stats_options_page_hook;
	//Add settings meta box
	add_meta_box(
		'xmpp_stats_settings_meta_box',
		__('Settings', 'xmpp_stats'),
		'xmpp_stats_settings_meta_box',
		$xmpp_stats_options_page_hook,
		'normal',
		'default'
	);
	//Add simple shortcodes meta box
	add_meta_box(
		'xmpp_stats_simple_shortcodes_meta_box',
		__('Simple shortcodes', 'xmpp_stats'),
		'xmpp_stats_simple_shortcodes_meta_box',
		$xmpp_stats_options_page_hook,
		'side',
		'default'
	);
	//Add graphs shortcodes meta box
	add_meta_box(
		'xmpp_stats_graphs_shortcodes_meta_box',
		__('Shortcodes for graphs', 'xmpp_stats'),
		'xmpp_stats_graphs_shortcodes_meta_box',
		$xmpp_stats_options_page_hook,
		'side',
		'default'
	);
}

//Settings meta box
function xmpp_stats_settings_meta_box() { ?>
	</div>
	<form id="xmpp-stats-form" method="post" action="options.php">
		<?php settings_fields('xmpp_stats_settings'); ?>
		<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
		<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); ?>
		<div class="inside" style="margin-top:-18px;">		
			<ul>
				<li>
					<label for="xmpp_stats_rest_url"><?php _e('REST API url', 'xmpp_stats'); ?>:&nbsp;<input type="text" size="40" style="max-width:100%;" name="xmpp_stats_rest_url" id="xmpp_stats_rest_url" value="<?php echo get_option('xmpp_stats_rest_url') ?>" /></label>
					</br><small><?php _e('Enter URL defined in module mod_rest in ejabberd settings.', 'xmpp_stats'); ?></small>
				</li>
				<li>
					<label for="xmpp_stats_uptime_url"><?php _e('URL to system uptime data', 'xmpp_stats'); ?>:&nbsp;<input type="text" size="40" style="max-width:100%;" name="xmpp_stats_uptime_url" id="xmpp_stats_uptime_url" value="<?php echo get_option('xmpp_stats_uptime_url') ?>" /></label>
					</br><small><?php _e('Enter URL defined in module mod_http_fileserver in ejabberd settings which returns system boot time in UNIX TimeStamp.', 'xmpp_stats'); ?></small>
					</br><small><?php _e('Example to get system boot time', 'xmpp_stats'); ?>: <i>cat /proc/stat | grep btime | awk '{ print $2 }' > /tmp/ejabberd/uptime.html</i></small>
				</li>
				<li>
					<label for="xmpp_stats_save_data"><input type="checkbox" id="xmpp_stats_save_data" name="xmpp_stats_save_data" value="1" <?php echo checked(1, get_option('xmpp_stats_save_data'), false ); ?> /><?php _e('Save statistics', 'xmpp_stats'); ?></label>
					</br><small><?php _e('Automatically retrieves server statistics every 5 minutes and stores them in a database.', 'xmpp_stats'); ?></small>
				</li>
				<li>
					<label for="xmpp_stats_auth"><input type="checkbox" id="xmpp_stats_auth" name="xmpp_stats_auth" value="1" <?php echo checked(1, get_option('xmpp_stats_auth'), false ); ?> /><?php _e('Enable authorization', 'xmpp_stats'); ?></label>
					</br><label for="xmpp_stats_login"><?php _e('Login', 'xmpp_stats'); ?>:&nbsp;<input type="text" size="40" name="xmpp_stats_login" id="xmpp_stats_login" value="<?php echo get_option('xmpp_stats_login') ?>" /></label>
					</br><label for="xmpp_stats_password"><?php _e('Password', 'xmpp_stats'); ?>:&nbsp;<input type="password" size="40" name="xmpp_stats_password" id="xmpp_stats_password" value="<?php echo get_option('xmpp_stats_password') ?>" /></label>
					</br><label for="xmpp_stats_set_last"><input type="checkbox" id="xmpp_stats_set_last" name="xmpp_stats_set_last" value="1" <?php echo checked(1, get_option('xmpp_stats_set_last'), false ); ?> /><?php _e('Set last activity information', 'xmpp_stats'); ?></label>
				</li>
			</ul>		
		</div>
		<div id="major-publishing-actions">
			<div id="publishing-action">
				<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save settings', 'xmpp_stats'); ?>" />
			</div>
			<div class="clear"></div>
		</div>
	</form>
	<div>
<?php }

function xmpp_stats_simple_shortcodes_meta_box() { ?>
	<ul>
		<li><b>[xmpp_onlineusers]</b></br><?php _e('Online users count', 'xmpp_stats'); ?></br><small><?php _e('Command', 'xmpp_stats'); ?>:&nbsp;ejabberdctl stats onlineusers</small></li>
		<li><b>[xmpp_registeredusers]</b></br><?php _e('Registered users count', 'xmpp_stats'); ?></br><small><?php _e('Command', 'xmpp_stats'); ?>:&nbsp;ejabberdctl stats registeredusers</small></li>
		<li><b>[xmpp_s2s_out]</b></br><?php _e('Outgoing s2s connections count', 'xmpp_stats'); ?></br><small><?php _e('Command', 'xmpp_stats'); ?>:&nbsp;ejabberdctl getstatsdx s2sconnections</small></li>
		<li><b>[xmpp_s2s_in]</b></br><?php _e('Incomming s2s connections count', 'xmpp_stats'); ?></br><small><?php _e('Command', 'xmpp_stats'); ?>:&nbsp;ejabberdctl getstatsdx s2sservers</small></li>
		<li><b>[xmpp_uptime]</b></br><?php _e('XMPP server uptime', 'xmpp_stats'); ?></br><small><?php _e('Command', 'xmpp_stats'); ?>:&nbsp;ejabberdctl stats uptimeseconds</small></li>
		<li><b>[system_uptime]</b></br><?php _e('System uptime', 'xmpp_stats'); ?></li>
	</ul>
<?php }

function xmpp_stats_graphs_shortcodes_meta_box() { ?>
	<ul>
		<li><b>[xmpp_onlineusers_day_graph]</b></br><?php _e('Logged in users - by day', 'xmpp_stats'); ?></li>
		<li><b>[xmpp_onlineusers_week_graph]</b></br><?php _e('Logged in users - by week', 'xmpp_stats'); ?></li>
		<li><b>[xmpp_registeredusers_day_graph]</b></br><?php _e('Registered users - by day', 'xmpp_stats'); ?></li>
		<li><b>[xmpp_registeredusers_week_graph]</b></br><?php _e('Registered users - by week', 'xmpp_stats'); ?></li>
		<li><b>[xmpp_s2s_day_graph]</b></br><?php _e('S2S connections - by day', 'xmpp_stats'); ?></li>
		<li><b>[xmpp_s2s_week_graph]</b></br><?php _e('S2S connections - by week', 'xmpp_stats'); ?></li>
		<li><b>[xmpp_uptime_day_graph]</b></br><?php _e('XMPP server uptime - by day', 'xmpp_stats'); ?></li>
		<li><b>[xmpp_uptime_week_graph]</b></br><?php _e('XMPP server uptime - by week', 'xmpp_stats'); ?></li>
		<li><b>[system_uptime_day_graph]</b></br><?php _e('System uptime - by day', 'xmpp_stats'); ?></li>
		<li><b>[system_uptime_week_graph]</b></br><?php _e('System uptime - by week', 'xmpp_stats'); ?></li>
	</ul>
<?php }

//Display options page
function xmpp_stats_options() {
	//Global variable
	global $xmpp_stats_options_page_hook;
	//Enable add_meta_boxes function
	do_action('add_meta_boxes', $xmpp_stats_options_page_hook); ?>
	<div class="wrap">
		<h2><?php _e('XMPP server statistics', 'xmpp_stats'); ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes($xmpp_stats_options_page_hook, 'normal', null); ?>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes($xmpp_stats_options_page_hook, 'side', null); ?>
				</div>
			</div>
		</div>
	</div>
<?php }
