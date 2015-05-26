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

//Show online users day graph
function shortcode_xmpp_onlineusers_day_graph() {
	//Add styles & sripts
	wp_enqueue_style('flot', plugin_dir_url(__FILE__).'css/flot.css', array(), filemtime(plugin_dir_path(__FILE__).'css/flot.css'), false);
	wp_enqueue_script('flot', plugin_dir_url(__FILE__).'js/jquery.flot.min.js', array(), '0.8.3', true);
	wp_enqueue_script('flot-axislabels', plugin_dir_url(__FILE__).'js/jquery.flot.axislabels.js', array(), '2.0', true);
	wp_enqueue_script('flot-time', plugin_dir_url(__FILE__).'js/jquery.flot.time.min.js', array(), '0.8.3', true);
	//Add jQuery script
	function shortcode_xmpp_onlineusers_day_graph_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			//Graph data
			var data = [{
				color: '<?php echo get_option('xmpp_stats_graph_line_color'); ?>',
				label: '<?php _e('Logged in users', 'xmpp_stats'); ?>',
				<?php //Datebase data
				global $wpdb;
				$table_name = $wpdb->prefix . 'xmpp_stats';
				//Get latest record
				$row = $wpdb->get_row("SELECT * FROM $table_name WHERE type = '1' ORDER BY timestamp DESC");
				//Calculation oldest date for select
				$oldest = date_i18n('Y-m-d H:i:s', strtotime($row->timestamp)-(24*60*60));
				//Get data from the last 24 hours
				$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '1' AND timestamp > '$oldest' ORDER BY timestamp ASC");
				foreach($rows as $row) {
					$timestamp = strtotime($row->timestamp);
					if($row === reset($rows))
						echo 'data: [ ['.$timestamp.'000, '.$row->value.'], ';
					else if($row === end($rows))
						echo '['.$timestamp.'000, '.$row->value.'] ]'."\n";
					else
						echo '['.$timestamp.'000, '.$row->value.'], ';
				} ?>
			}];
			//Graph options
			var options = {
				xaxis: {
					mode: 'time',
					timezone: 'browser',
					tickSize: [4, 'hour'],
					timeformat: '%a</br>%H:%S',
					dayNames: ['<?php _e('sun', 'xmpp_stats'); ?>', '<?php _e('mon', 'xmpp_stats'); ?>', '<?php _e('tue', 'xmpp_stats'); ?>', '<?php _e('wed', 'xmpp_stats'); ?>', '<?php _e('thu', 'xmpp_stats'); ?>', '<?php _e('fri', 'xmpp_stats'); ?>', '<?php _e('sat', 'xmpp_stats'); ?>']
				},
				yaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						lineWidth: 1
					},
					shadowSize: 0
				},
				grid: {
					clickable: true,
					hoverable: true,
					color: '<?php echo get_option('xmpp_stats_graph_grid_color'); ?>',
					borderWidth: 1
				},
				legend: {
					show: false
				}
			};
			//Draw graph
			$.plot('#xmpp_onlineusers_day_graph', data, options);
			//Show tooltip
			function showTooltip(x, y, contents) {
				$('<div id="flot-tooltip">' + contents + '</div>').css({
					top: y - 16,
					left: x + 20
				}).appendTo('body').fadeIn(200);
			}
			var previousPoint = null;
			$('#xmpp_onlineusers_day_graph').bind('plothover', function (event, pos, item) {
				if(item) {
					if(previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;
						$('#flot-tooltip').remove();
						var x = item.datapoint[0],
							y = item.datapoint[1];

						var userTZ = new Date();
						userTZ = userTZ.getTimezoneOffset() * 3600 * 1000;
						var dt = new Date(x + userTZ);
						var date = dt.toLocaleTimeString();

						showTooltip(item.pageX, item.pageY, y + ' <?php _e('users at', 'xmpp_stats'); ?> ' + date);
					}
				} else {
					$('#flot-tooltip').remove();
					previousPoint = null;
				}
			});
			//Redraw graph
			$(window).on("resize", function( event ) {
				$.plot('#xmpp_onlineusers_day_graph', data, options);
			});
		});
		</script> <?php
	}
	add_action('wp_footer', 'shortcode_xmpp_onlineusers_day_graph_jquery');
	return '<div class="graph-container"><h3>'.__('Logged in users - by day', 'xmpp_stats').'</h3><div id="xmpp_onlineusers_day_graph" class="graph-placeholder"></div></div>';
}

//Show online users week graph
function shortcode_xmpp_onlineusers_week_graph() {
	//Add styles & sripts
	wp_enqueue_style('flot', plugin_dir_url(__FILE__).'css/flot.css', array(), '1.0', false);
	wp_enqueue_script('flot', plugin_dir_url(__FILE__).'js/jquery.flot.min.js', array(), '0.8.3', true);
	wp_enqueue_script('flot-axislabels', plugin_dir_url(__FILE__).'js/jquery.flot.axislabels.js', array(), '2.0', true);
	wp_enqueue_script('flot-time', plugin_dir_url(__FILE__).'js/jquery.flot.time.min.js', array(), '0.8.3', true);
	//Add jQuery script
	function shortcode_xmpp_onlineusers_week_graph_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			//Graph data
			var data = [{
				color: '<?php echo get_option('xmpp_stats_graph_line_color'); ?>',
				label: '<?php _e('Logged in users', 'xmpp_stats'); ?>',
				<?php //Datebase data
				global $wpdb;
				$table_name = $wpdb->prefix . 'xmpp_stats';
				//Get latest record
				$row = $wpdb->get_row("SELECT * FROM $table_name WHERE type = '1' ORDER BY timestamp DESC");
				//Calculation oldest date for select
				$oldest = date_i18n('Y-m-d H:i:s', strtotime($row->timestamp)-(7*24*60*60));
				//Get data from the last 24 hours
				$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '1' AND timestamp > '$oldest' ORDER BY timestamp ASC");
				foreach($rows as $row) {
					$timestamp = strtotime($row->timestamp);
					if($row === reset($rows))
						echo 'data: [ ['.$timestamp.'000, '.$row->value.'], ';
					else if($row === end($rows))
						echo '['.$timestamp.'000, '.$row->value.'] ]'."\n";
					else
						echo '['.$timestamp.'000, '.$row->value.'], ';
				} ?>
			}];
			//Graph options
			var options = {
				xaxis: {
					mode: 'time',
					timezone: 'browser',
					tickSize: [1, 'day'],
					timeformat: '%a</br>%e.%m',
					dayNames: ['<?php _e('sun', 'xmpp_stats'); ?>', '<?php _e('mon', 'xmpp_stats'); ?>', '<?php _e('tue', 'xmpp_stats'); ?>', '<?php _e('wed', 'xmpp_stats'); ?>', '<?php _e('thu', 'xmpp_stats'); ?>', '<?php _e('fri', 'xmpp_stats'); ?>', '<?php _e('sat', 'xmpp_stats'); ?>']
				},
				yaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						lineWidth: 1
					},
					shadowSize: 0
				},
				grid: {
					clickable: true,
					hoverable: true,
					color: '<?php echo get_option('xmpp_stats_graph_grid_color'); ?>',
					borderWidth: 1
				},
				legend: {
					show: false
				}
			};
			//Draw graph
			$.plot('#xmpp_onlineusers_week_graph', data, options);
			//Show tooltip
			function showTooltip(x, y, contents) {
				$('<div id="flot-tooltip">' + contents + '</div>').css({
					top: y - 16,
					left: x + 20
				}).appendTo('body').fadeIn(200);
			}
			var previousPoint = null;
			$('#xmpp_onlineusers_week_graph').bind('plothover', function (event, pos, item) {
				if(item) {
					if(previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;
						$('#flot-tooltip').remove();
						var x = item.datapoint[0],
							y = item.datapoint[1];

						var userTZ = new Date();
						userTZ = userTZ.getTimezoneOffset() * 3600 * 1000;
						var dt = new Date(x + userTZ);
						var date = dt.toLocaleTimeString();

						showTooltip(item.pageX, item.pageY, y + ' <?php _e('users at', 'xmpp_stats'); ?> ' + date);
					}
				} else {
					$('#flot-tooltip').remove();
					previousPoint = null;
				}
			});
			//Redraw graph
			$(window).on("resize", function( event ) {
				$.plot('#xmpp_onlineusers_week_graph', data, options);
			});
		});
		</script> <?php
	}
	add_action('wp_footer', 'shortcode_xmpp_onlineusers_week_graph_jquery');
	return '<div class="graph-container"><h3>'.__('Logged in users - by week', 'xmpp_stats').'</h3><div id="xmpp_onlineusers_week_graph" class="graph-placeholder"></div></div>';
}

//Show registered users day graph
function shortcode_xmpp_registeredusers_day_graph() {
	//Add styles & sripts
	wp_enqueue_style('flot', plugin_dir_url(__FILE__).'css/flot.css', array(), '1.0', false);
	wp_enqueue_script('flot', plugin_dir_url(__FILE__).'js/jquery.flot.min.js', array(), '0.8.3', true);
	wp_enqueue_script('flot-axislabels', plugin_dir_url(__FILE__).'js/jquery.flot.axislabels.js', array(), '2.0', true);
	wp_enqueue_script('flot-time', plugin_dir_url(__FILE__).'js/jquery.flot.time.min.js', array(), '0.8.3', true);
	//Add jQuery script
	function shortcode_xmpp_registeredusers_day_graph_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			//Graph data
			var data = [{
				color: '<?php echo get_option('xmpp_stats_graph_line_color'); ?>',
				label: '<?php _e('Registered users', 'xmpp_stats'); ?>',
				<?php //Datebase data
				global $wpdb;
				$table_name = $wpdb->prefix . 'xmpp_stats';
				//Get latest record
				$row = $wpdb->get_row("SELECT * FROM $table_name WHERE type = '2' ORDER BY timestamp DESC");
				//Calculation oldest date for select
				$oldest = date_i18n('Y-m-d H:i:s', strtotime($row->timestamp)-(24*60*60));
				//Get data from the last 24 hours
				$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '2' AND timestamp > '$oldest' ORDER BY timestamp ASC");
				foreach($rows as $row) {
					$timestamp = strtotime($row->timestamp);
					if($row === reset($rows))
						echo 'data: [ ['.$timestamp.'000, '.$row->value.'], ';
					else if($row === end($rows))
						echo '['.$timestamp.'000, '.$row->value.'] ]'."\n";
					else
						echo '['.$timestamp.'000, '.$row->value.'], ';
				} ?>
			}];
			//Graph options
			var options = {
				xaxis: {
					mode: 'time',
					timezone: 'browser',
					tickSize: [4, 'hour'],
					timeformat: '%a</br>%H:%S',
					dayNames: ['<?php _e('sun', 'xmpp_stats'); ?>', '<?php _e('mon', 'xmpp_stats'); ?>', '<?php _e('tue', 'xmpp_stats'); ?>', '<?php _e('wed', 'xmpp_stats'); ?>', '<?php _e('thu', 'xmpp_stats'); ?>', '<?php _e('fri', 'xmpp_stats'); ?>', '<?php _e('sat', 'xmpp_stats'); ?>']
				},
				yaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						lineWidth: 1
					},
					shadowSize: 0
				},
				grid: {
					clickable: true,
					hoverable: true,
					color: '<?php echo get_option('xmpp_stats_graph_grid_color'); ?>',
					borderWidth: 1
				},
				legend: {
					show: false
				}
			};
			//Draw graph
			$.plot('#xmpp_registeredusers_day_graph', data, options);
			//Show tooltip
			function showTooltip(x, y, contents) {
				$('<div id="flot-tooltip">' + contents + '</div>').css({
					top: y - 16,
					left: x + 20
				}).appendTo('body').fadeIn(200);
			}
			var previousPoint = null;
			$('#xmpp_registeredusers_day_graph').bind('plothover', function (event, pos, item) {
				if(item) {
					if(previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;
						$('#flot-tooltip').remove();
						var x = item.datapoint[0],
							y = item.datapoint[1];

						var userTZ = new Date();
						userTZ = userTZ.getTimezoneOffset() * 3600 * 1000;
						var dt = new Date(x + userTZ);
						var date = dt.toLocaleTimeString();

						showTooltip(item.pageX, item.pageY, y + ' <?php _e('users at', 'xmpp_stats'); ?> ' + date);
					}
				} else {
					$('#flot-tooltip').remove();
					previousPoint = null;
				}
			});
			//Redraw graph
			$(window).on("resize", function( event ) {
				$.plot('#xmpp_registeredusers_day_graph', data, options);
			});
		});
		</script> <?php
	}
	add_action('wp_footer', 'shortcode_xmpp_registeredusers_day_graph_jquery');
	return '<div class="graph-container"><h3>'.__('Registered users - by day', 'xmpp_stats').'</h3><div id="xmpp_registeredusers_day_graph" class="graph-placeholder"></div></div>';
}

//Show registered users week graph
function shortcode_xmpp_registeredusers_week_graph() {
	//Add styles & sripts
	wp_enqueue_style('flot', plugin_dir_url(__FILE__).'css/flot.css', array(), '1.0', false);
	wp_enqueue_script('flot', plugin_dir_url(__FILE__).'js/jquery.flot.min.js', array(), '0.8.3', true);
	wp_enqueue_script('flot-axislabels', plugin_dir_url(__FILE__).'js/jquery.flot.axislabels.js', array(), '2.0', true);
	wp_enqueue_script('flot-time', plugin_dir_url(__FILE__).'js/jquery.flot.time.min.js', array(), '0.8.3', true);
	//Add jQuery script
	function shortcode_xmpp_registeredusers_week_graph_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			//Graph data
			var data = [{
				color: '<?php echo get_option('xmpp_stats_graph_line_color'); ?>',
				label: '<?php _e('Registered users', 'xmpp_stats'); ?>',
				<?php //Datebase data
				global $wpdb;
				$table_name = $wpdb->prefix . 'xmpp_stats';
				//Get latest record
				$row = $wpdb->get_row("SELECT * FROM $table_name WHERE type = '2' ORDER BY timestamp DESC");
				//Calculation oldest date for select
				$oldest = date_i18n('Y-m-d H:i:s', strtotime($row->timestamp)-(7*24*60*60));
				//Get data from the last 24 hours
				$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '2' AND timestamp > '$oldest' ORDER BY timestamp ASC");
				foreach($rows as $row) {
					$timestamp = strtotime($row->timestamp);
					if($row === reset($rows))
						echo 'data: [ ['.$timestamp.'000, '.$row->value.'], ';
					else if($row === end($rows))
						echo '['.$timestamp.'000, '.$row->value.'] ]'."\n";
					else
						echo '['.$timestamp.'000, '.$row->value.'], ';
				} ?>
			}];
			//Graph options
			var options = {
				xaxis: {
					mode: 'time',
					timezone: 'browser',
					tickSize: [1, 'day'],
					timeformat: '%a</br>%e.%m',
					dayNames: ['<?php _e('sun', 'xmpp_stats'); ?>', '<?php _e('mon', 'xmpp_stats'); ?>', '<?php _e('tue', 'xmpp_stats'); ?>', '<?php _e('wed', 'xmpp_stats'); ?>', '<?php _e('thu', 'xmpp_stats'); ?>', '<?php _e('fri', 'xmpp_stats'); ?>', '<?php _e('sat', 'xmpp_stats'); ?>']
				},
				yaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						lineWidth: 1
					},
					shadowSize: 0
				},
				grid: {
					clickable: true,
					hoverable: true,
					color: '<?php echo get_option('xmpp_stats_graph_grid_color'); ?>',
					borderWidth: 1
				},
				legend: {
					show: false
				}
			};
			//Draw graph
			$.plot('#xmpp_registeredusers_week_graph', data, options);
			//Show tooltip
			function showTooltip(x, y, contents) {
				$('<div id="flot-tooltip">' + contents + '</div>').css({
					top: y - 16,
					left: x + 20
				}).appendTo('body').fadeIn(200);
			}
			var previousPoint = null;
			$('#xmpp_registeredusers_week_graph').bind('plothover', function (event, pos, item) {
				if(item) {
					if(previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;
						$('#flot-tooltip').remove();
						var x = item.datapoint[0],
							y = item.datapoint[1];

						var userTZ = new Date();
						userTZ = userTZ.getTimezoneOffset() * 3600 * 1000;
						var dt = new Date(x + userTZ);
						var date = dt.toLocaleTimeString();

						showTooltip(item.pageX, item.pageY, y + ' <?php _e('users at', 'xmpp_stats'); ?> ' + date);
					}
				} else {
					$('#flot-tooltip').remove();
					previousPoint = null;
				}
			});
			//Redraw graph
			$(window).on("resize", function( event ) {
				$.plot('#xmpp_registeredusers_week_graph', data, options);
			});
		});
		</script> <?php
	}
	add_action('wp_footer', 'shortcode_xmpp_registeredusers_week_graph_jquery');
	return '<div class="graph-container"><h3>'.__('Registered users - by week', 'xmpp_stats').'</h3><div id="xmpp_registeredusers_week_graph" class="graph-placeholder"></div></div>';
}

//Show S2S connections day graph
function shortcode_xmpp_s2s_day_graph() {
	//Add styles & sripts
	wp_enqueue_style('flot', plugin_dir_url(__FILE__).'css/flot.css', array(), '1.0', false);
	wp_enqueue_script('flot', plugin_dir_url(__FILE__).'js/jquery.flot.min.js', array(), '0.8.3', true);
	wp_enqueue_script('flot-axislabels', plugin_dir_url(__FILE__).'js/jquery.flot.axislabels.js', array(), '2.0', true);
	wp_enqueue_script('flot-time', plugin_dir_url(__FILE__).'js/jquery.flot.time.min.js', array(), '0.8.3', true);
	//Add jQuery script
	function shortcode_xmpp_s2s_day_graph_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			//Graph data
			var datasets = {
					'outgoing': {
						color: '<?php echo get_option('xmpp_stats_graph_line_color'); ?>',
						label: '<?php _e('Outgoing connections', 'xmpp_stats'); ?>',
						caption: '<?php _e('outgoing connections', 'xmpp_stats'); ?>',
						<?php //Datebase data
						global $wpdb;
						$table_name = $wpdb->prefix . 'xmpp_stats';
						//Get latest record
						$row = $wpdb->get_row("SELECT * FROM $table_name WHERE type = '3' ORDER BY timestamp DESC");
						//Calculation oldest date for select
						$oldest = date_i18n('Y-m-d H:i:s', strtotime($row->timestamp)-(24*60*60));
						//Get data from the last 24 hours
						$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '3' AND timestamp > '$oldest' ORDER BY timestamp ASC");
						foreach($rows as $row) {
							$timestamp = strtotime($row->timestamp);
							if($row === reset($rows))
								echo 'data: [ ['.$timestamp.'000, '.$row->value.'], ';
							else if($row === end($rows))
								echo '['.$timestamp.'000, '.$row->value.'] ]'."\n";
							else
								echo '['.$timestamp.'000, '.$row->value.'], ';
						} ?>
					},
					'incoming': {
						color: '<?php echo get_option('xmpp_stats_graph_line_color2'); ?>',
						label: '<?php _e('Incoming connections', 'xmpp_stats'); ?>',
						caption: '<?php _e('incoming connections', 'xmpp_stats'); ?>',
						<?php //Get data from the last 24 hours
						$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '4' AND timestamp > '$oldest' ORDER BY timestamp ASC");
						foreach($rows as $row) {
							$timestamp = strtotime($row->timestamp);
							if($row === reset($rows))
								echo 'data: [ ['.$timestamp.'000, '.$row->value.'], ';
							else if($row === end($rows))
								echo '['.$timestamp.'000, '.$row->value.'] ]'."\n";
							else
								echo '['.$timestamp.'000, '.$row->value.'], ';
						} ?>
					}
			};
			//Graph options
			var options = {
				xaxis: {
					mode: 'time',
					timezone: 'browser',
					tickSize: [4, 'hour'],
					timeformat: '%a</br>%H:%S',
					dayNames: ['<?php _e('sun', 'xmpp_stats'); ?>', '<?php _e('mon', 'xmpp_stats'); ?>', '<?php _e('tue', 'xmpp_stats'); ?>', '<?php _e('wed', 'xmpp_stats'); ?>', '<?php _e('thu', 'xmpp_stats'); ?>', '<?php _e('fri', 'xmpp_stats'); ?>', '<?php _e('sat', 'xmpp_stats'); ?>']
				},
				yaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						lineWidth: 1
					},
					shadowSize: 0
				},
				grid: {
					clickable: true,
					hoverable: true,
					color: '<?php echo get_option('xmpp_stats_graph_grid_color'); ?>',
					borderWidth: 1
				},
				legend: {
					show: false
				}
			};
			//Insert checkboxes
			var choiceContainer = $('#xmpp_s2s_day_graph_choices');
			$.each(datasets, function(key, val) {
				choiceContainer.append('<div><input type="checkbox" name="' + key +
					'" checked="checked" id="xmpp_s2s_day_graph_choices_' + key + '"></input>' +
					'<label for="xmpp_s2s_day_graph_choices_' + key + '" style="color:' + val.color + ';">'
					+ val.label + '</label></div>');
			});
			choiceContainer.find('input').click(plotAccordingToChoices);
			//Insert graph
			function plotAccordingToChoices() {
				var data = [];
				choiceContainer.find('input:checked').each(function () {
					var key = $(this).attr('name');
					if(key && datasets[key]) {
						data.push(datasets[key]);
					}
				});
				//Draw graph
				if(data.length > 0) {
					$.plot('#xmpp_s2s_day_graph', data, options);
				}
			}
			plotAccordingToChoices();
			//Show tooltip
			function showTooltip(x, y, contents) {
				$('<div id="flot-tooltip">' + contents + '</div>').css({
					top: y - 16,
					left: x + 20
				}).appendTo('body').fadeIn(200);
			}
			var previousPoint = null;
			$('#xmpp_s2s_day_graph').bind('plothover', function (event, pos, item) {
				if(item) {
					if(previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;
						$('#flot-tooltip').remove();
						var x = item.datapoint[0],
							y = item.datapoint[1];

						var userTZ = new Date();
						userTZ = userTZ.getTimezoneOffset() * 3600 * 1000;
						var dt = new Date(x + userTZ);
						var date = dt.toLocaleTimeString();

						showTooltip(item.pageX, item.pageY, y + ' ' + item.series.caption + ' <?php _e('at', 'xmpp_stats'); ?> ' + date);
					}
				} else {
					$('#flot-tooltip').remove();
					previousPoint = null;
				}
			});
			//Redraw graph
			$(window).on("resize", function( event ) {
				plotAccordingToChoices();
			});
		});
		</script> <?php
	}
	add_action('wp_footer', 'shortcode_xmpp_s2s_day_graph_jquery');
	return '<div class="graph-container"><h3>'.__('S2S connections - by day', 'xmpp_stats').'</h3><div id="xmpp_s2s_day_graph" class="graph-placeholder"></div><div id="xmpp_s2s_day_graph_choices" class="graph-choices"></div></div>';
}

//Show S2S connections week graph
function shortcode_xmpp_s2s_week_graph() {
	//Add styles & sripts
	wp_enqueue_style('flot', plugin_dir_url(__FILE__).'css/flot.css', array(), '1.0', false);
	wp_enqueue_script('flot', plugin_dir_url(__FILE__).'js/jquery.flot.min.js', array(), '0.8.3', true);
	wp_enqueue_script('flot-axislabels', plugin_dir_url(__FILE__).'js/jquery.flot.axislabels.js', array(), '2.0', true);
	wp_enqueue_script('flot-time', plugin_dir_url(__FILE__).'js/jquery.flot.time.min.js', array(), '0.8.3', true);
	//Add jQuery script
	function shortcode_xmpp_s2s_week_graph_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			//Graph data
			var datasets = {
					'outgoing': {
						color: '<?php echo get_option('xmpp_stats_graph_line_color'); ?>',
						label: '<?php _e('Outgoing connections', 'xmpp_stats'); ?>',
						caption: '<?php _e('outgoing connections', 'xmpp_stats'); ?>',
						<?php //Datebase data
						global $wpdb;
						$table_name = $wpdb->prefix . 'xmpp_stats';
						//Get latest record
						$row = $wpdb->get_row("SELECT * FROM $table_name WHERE type = '3' ORDER BY timestamp DESC");
						//Calculation oldest date for select
						$oldest = date_i18n('Y-m-d H:i:s', strtotime($row->timestamp)-(7*24*60*60));
						//Get data from the last week
						$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '3' AND timestamp > '$oldest' ORDER BY timestamp ASC");
						foreach($rows as $row) {
							$timestamp = strtotime($row->timestamp);
							if($row === reset($rows))
								echo 'data: [ ['.$timestamp.'000, '.$row->value.'], ';
							else if($row === end($rows))
								echo '['.$timestamp.'000, '.$row->value.'] ]'."\n";
							else
								echo '['.$timestamp.'000, '.$row->value.'], ';
						} ?>
					},
					'incoming': {
						color: '#0066B3',
						label: '<?php _e('Incoming connections', 'xmpp_stats'); ?>',
						caption: '<?php _e('incoming connections', 'xmpp_stats'); ?>',
						<?php //Get data from the last week
						$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '4' AND timestamp > '$oldest' ORDER BY timestamp ASC");
						foreach($rows as $row) {
							$timestamp = strtotime($row->timestamp);
							if($row === reset($rows))
								echo 'data: [ ['.$timestamp.'000, '.$row->value.'], ';
							else if($row === end($rows))
								echo '['.$timestamp.'000, '.$row->value.'] ]'."\n";
							else
								echo '['.$timestamp.'000, '.$row->value.'], ';
						} ?>
					}
			};
			//Graph options
			var options = {
				xaxis: {
					mode: 'time',
					timezone: 'browser',
					tickSize: [1, 'day'],
					timeformat: '%a</br>%e.%m',
					dayNames: ['<?php _e('sun', 'xmpp_stats'); ?>', '<?php _e('mon', 'xmpp_stats'); ?>', '<?php _e('tue', 'xmpp_stats'); ?>', '<?php _e('wed', 'xmpp_stats'); ?>', '<?php _e('thu', 'xmpp_stats'); ?>', '<?php _e('fri', 'xmpp_stats'); ?>', '<?php _e('sat', 'xmpp_stats'); ?>']
				},
				yaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						lineWidth: 1
					},
					shadowSize: 0
				},
				grid: {
					clickable: true,
					hoverable: true,
					color: '<?php echo get_option('xmpp_stats_graph_grid_color'); ?>',
					borderWidth: 1
				},
				legend: {
					show: false
				}
			};
			//Insert checkboxes
			var choiceContainer = $('#xmpp_s2s_week_graph_choices');
			$.each(datasets, function(key, val) {
				choiceContainer.append('<div><input type="checkbox" name="' + key +
					'" checked="checked" id="xmpp_s2s_week_graph_choices_' + key + '"></input>' +
					'<label for="xmpp_s2s_week_graph_choices_' + key + '" style="color:' + val.color + ';">'
					+ val.label + '</label></div>');
			});
			choiceContainer.find('input').click(plotAccordingToChoices);
			//Insert graph
			function plotAccordingToChoices() {
				var data = [];
				choiceContainer.find('input:checked').each(function () {
					var key = $(this).attr('name');
					if(key && datasets[key]) {
						data.push(datasets[key]);
					}
				});
				//Draw graph
				if(data.length > 0) {
					$.plot('#xmpp_s2s_week_graph', data, options);
				}
			}
			plotAccordingToChoices();
			//Show tooltip
			function showTooltip(x, y, contents) {
				$('<div id="flot-tooltip">' + contents + '</div>').css({
					top: y - 16,
					left: x + 20
				}).appendTo('body').fadeIn(200);
			}
			var previousPoint = null;
			$('#xmpp_s2s_week_graph').bind('plothover', function (event, pos, item) {
				if(item) {
					if(previousPoint != item.dataIndex) {
						previousPoint = item.dataIndex;
						$('#flot-tooltip').remove();
						var x = item.datapoint[0],
							y = item.datapoint[1];

						var userTZ = new Date();
						userTZ = userTZ.getTimezoneOffset() * 3600 * 1000;
						var dt = new Date(x + userTZ);
						var date = dt.toLocaleTimeString();

						showTooltip(item.pageX, item.pageY, y + ' ' + item.series.caption + ' <?php _e('at', 'xmpp_stats'); ?> ' + date);
					}
				} else {
					$('#flot-tooltip').remove();
					previousPoint = null;
				}
			});
			//Redraw graph
			$(window).on("resize", function( event ) {
				plotAccordingToChoices();
			});
		});
		</script> <?php
	}
	add_action('wp_footer', 'shortcode_xmpp_s2s_week_graph_jquery');
	return '<div class="graph-container"><h3>'.__('S2S connections - by week', 'xmpp_stats').'</h3><div id="xmpp_s2s_week_graph" class="graph-placeholder"></div><div id="xmpp_s2s_week_graph_choices" class="graph-choices"></div></div>';
}

//Show XMPP server uptime day graph
function shortcode_xmpp_uptime_day_graph() {
	//Add styles & sripts
	wp_enqueue_style('flot', plugin_dir_url(__FILE__).'css/flot.css', array(), filemtime(plugin_dir_path(__FILE__).'css/flot.css'), false);
	wp_enqueue_script('flot', plugin_dir_url(__FILE__).'js/jquery.flot.min.js', array(), '0.8.3', true);
	wp_enqueue_script('flot-axislabels', plugin_dir_url(__FILE__).'js/jquery.flot.axislabels.js', array(), '2.0', true);
	wp_enqueue_script('flot-time', plugin_dir_url(__FILE__).'js/jquery.flot.time.min.js', array(), '0.8.3', true);
	//Add jQuery script
	function shortcode_xmpp_uptime_day_graph_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			//Graph data
			var data = [{
				color: '<?php echo get_option('xmpp_stats_graph_line_color'); ?>',
				label: '<?php _e('Uptime', 'xmpp_stats'); ?>',
				<?php //Datebase data
				global $wpdb;
				$table_name = $wpdb->prefix . 'xmpp_stats';
				//Get latest record
				$row = $wpdb->get_row("SELECT * FROM $table_name WHERE type = '5' ORDER BY timestamp DESC");
				//Calculation oldest date for select
				$oldest = date_i18n('Y-m-d H:i:s', strtotime($row->timestamp)-(24*60*60));
				//Get data from the last 24 hours
				$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '5' AND timestamp > '$oldest' ORDER BY timestamp ASC");
				foreach($rows as $row) {
					$timestamp = strtotime($row->timestamp);
					if($row === reset($rows))
						echo 'data: [ ['.$timestamp.'000, '.($row->value/(60*60*24)).'], ';
					else if($row === end($rows))
						echo '['.$timestamp.'000, '.($row->value/(60*60*24)).'] ]'."\n";
					else
						echo '['.$timestamp.'000, '.($row->value/(60*60*24)).'], ';
				} ?>
			}];
			//Graph options
			var options = {
				xaxis: {
					mode: 'time',
					timezone: 'browser',
					tickSize: [4, 'hour'],
					timeformat: '%a</br>%H:%S',
					dayNames: ['<?php _e('sun', 'xmpp_stats'); ?>', '<?php _e('mon', 'xmpp_stats'); ?>', '<?php _e('tue', 'xmpp_stats'); ?>', '<?php _e('wed', 'xmpp_stats'); ?>', '<?php _e('thu', 'xmpp_stats'); ?>', '<?php _e('fri', 'xmpp_stats'); ?>', '<?php _e('sat', 'xmpp_stats'); ?>']
				},
				yaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						lineWidth: 0,
						fill: true
					},
					shadowSize: 0
				},
				grid: {
					color: '<?php echo get_option('xmpp_stats_graph_grid_color'); ?>',
					borderWidth: 1
				},
				legend: {
					show: false
				}
			};
			//Draw graph
			$.plot('#xmpp_uptime_day_graph', data, options);
			//Redraw graph
			$(window).on("resize", function( event ) {
				$.plot('#xmpp_uptime_day_graph', data, options);
			});
		});
		</script> <?php
	}
	add_action('wp_footer', 'shortcode_xmpp_uptime_day_graph_jquery');
	return '<div class="graph-container"><h3>'.__('XMPP server uptime - by day', 'xmpp_stats').'</h3><div id="xmpp_uptime_day_graph" class="graph-placeholder"></div></div>';
}

//Show XMPP server uptime week graph
function shortcode_xmpp_uptime_week_graph() {
	//Add styles & sripts
	wp_enqueue_style('flot', plugin_dir_url(__FILE__).'css/flot.css', array(), '1.0', false);
	wp_enqueue_script('flot', plugin_dir_url(__FILE__).'js/jquery.flot.min.js', array(), '0.8.3', true);
	wp_enqueue_script('flot-axislabels', plugin_dir_url(__FILE__).'js/jquery.flot.axislabels.js', array(), '2.0', true);
	wp_enqueue_script('flot-time', plugin_dir_url(__FILE__).'js/jquery.flot.time.min.js', array(), '0.8.3', true);
	//Add jQuery script
	function shortcode_xmpp_uptime_week_graph_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			//Graph data
			var data = [{
				color: '<?php echo get_option('xmpp_stats_graph_line_color'); ?>',
				label: '<?php _e('Uptime', 'xmpp_stats'); ?>',
				<?php //Datebase data
				global $wpdb;
				$table_name = $wpdb->prefix . 'xmpp_stats';
				//Get latest record
				$row = $wpdb->get_row("SELECT * FROM $table_name WHERE type = '5' ORDER BY timestamp DESC");
				//Calculation oldest date for select
				$oldest = date_i18n('Y-m-d H:i:s', strtotime($row->timestamp)-(7*24*60*60));
				//Get data from the last 24 hours
				$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '5' AND timestamp > '$oldest' ORDER BY timestamp ASC");
				foreach($rows as $row) {
					$timestamp = strtotime($row->timestamp);
					if($row === reset($rows))
						echo 'data: [ ['.$timestamp.'000, '.($row->value/(60*60*24)).'], ';
					else if($row === end($rows))
						echo '['.$timestamp.'000, '.($row->value/(60*60*24)).'] ]'."\n";
					else
						echo '['.$timestamp.'000, '.($row->value/(60*60*24)).'], ';
				} ?>
			}];
			//Graph options
			var options = {
				xaxis: {
					mode: 'time',
					timezone: 'browser',
					tickSize: [1, 'day'],
					timeformat: '%a</br>%e.%m',
					dayNames: ['<?php _e('sun', 'xmpp_stats'); ?>', '<?php _e('mon', 'xmpp_stats'); ?>', '<?php _e('tue', 'xmpp_stats'); ?>', '<?php _e('wed', 'xmpp_stats'); ?>', '<?php _e('thu', 'xmpp_stats'); ?>', '<?php _e('fri', 'xmpp_stats'); ?>', '<?php _e('sat', 'xmpp_stats'); ?>']
				},
				yaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						lineWidth: 0,
						fill: true
					},
					shadowSize: 0
				},
				grid: {
					color: '<?php echo get_option('xmpp_stats_graph_grid_color'); ?>',
					borderWidth: 1
				},
				legend: {
					show: false
				}
			};
			//Draw graph
			$.plot('#xmpp_uptime_week_graph', data, options);
			//Redraw graph
			$(window).on("resize", function( event ) {
				$.plot('#xmpp_uptime_week_graph', data, options);
			});
		});
		</script> <?php
	}
	add_action('wp_footer', 'shortcode_xmpp_uptime_week_graph_jquery');
	return '<div class="graph-container"><h3>'.__('XMPP server uptime - by week', 'xmpp_stats').'</h3><div id="xmpp_uptime_week_graph" class="graph-placeholder"></div></div>';
}

//Show system uptime day graph
function shortcode_system_uptime_day_graph() {
	//Add styles & sripts
	wp_enqueue_style('flot', plugin_dir_url(__FILE__).'css/flot.css', array(), filemtime(plugin_dir_path(__FILE__).'css/flot.css'), false);
	wp_enqueue_script('flot', plugin_dir_url(__FILE__).'js/jquery.flot.min.js', array(), '0.8.3', true);
	wp_enqueue_script('flot-axislabels', plugin_dir_url(__FILE__).'js/jquery.flot.axislabels.js', array(), '2.0', true);
	wp_enqueue_script('flot-time', plugin_dir_url(__FILE__).'js/jquery.flot.time.min.js', array(), '0.8.3', true);
	//Add jQuery script
	function shortcode_system_uptime_day_graph_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			//Graph data
			var data = [{
				color: '<?php echo get_option('xmpp_stats_graph_line_color'); ?>',
				label: '<?php _e('Uptime', 'xmpp_stats'); ?>',
				<?php //Datebase data
				global $wpdb;
				$table_name = $wpdb->prefix . 'xmpp_stats';
				//Get latest record
				$row = $wpdb->get_row("SELECT * FROM $table_name WHERE type = '6' ORDER BY timestamp DESC");
				//Calculation oldest date for select
				$oldest = date_i18n('Y-m-d H:i:s', strtotime($row->timestamp)-(24*60*60));
				//Get data from the last 24 hours
				$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '6' AND timestamp > '$oldest' ORDER BY timestamp ASC");
				foreach($rows as $row) {
					$timestamp = strtotime($row->timestamp);
					if($row === reset($rows))
						echo 'data: [ ['.$timestamp.'000, '.($row->value/(60*60*24)).'], ';
					else if($row === end($rows))
						echo '['.$timestamp.'000, '.($row->value/(60*60*24)).'] ]'."\n";
					else
						echo '['.$timestamp.'000, '.($row->value/(60*60*24)).'], ';
				} ?>
			}];
			//Graph options
			var options = {
				xaxis: {
					mode: 'time',
					timezone: 'browser',
					tickSize: [4, 'hour'],
					timeformat: '%a</br>%H:%S',
					dayNames: ['<?php _e('sun', 'xmpp_stats'); ?>', '<?php _e('mon', 'xmpp_stats'); ?>', '<?php _e('tue', 'xmpp_stats'); ?>', '<?php _e('wed', 'xmpp_stats'); ?>', '<?php _e('thu', 'xmpp_stats'); ?>', '<?php _e('fri', 'xmpp_stats'); ?>', '<?php _e('sat', 'xmpp_stats'); ?>']
				},
				yaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						lineWidth: 0,
						fill: true
					},
					shadowSize: 0
				},
				grid: {
					color: '<?php echo get_option('xmpp_stats_graph_grid_color'); ?>',
					borderWidth: 1
				},
				legend: {
					show: false
				}
			};
			//Draw graph
			$.plot('#system_uptime_day_graph', data, options);
			//Redraw graph
			$(window).on("resize", function( event ) {
				$.plot('#system_uptime_day_graph', data, options);
			});
		});
		</script> <?php
	}
	add_action('wp_footer', 'shortcode_system_uptime_day_graph_jquery');
	return '<div class="graph-container"><h3>'.__('System uptime - by day', 'xmpp_stats').'</h3><div id="system_uptime_day_graph" class="graph-placeholder"></div></div>';
}

//Show system uptime week graph
function shortcode_system_uptime_week_graph() {
	//Add styles & sripts
	wp_enqueue_style('flot', plugin_dir_url(__FILE__).'css/flot.css', array(), '1.0', false);
	wp_enqueue_script('flot', plugin_dir_url(__FILE__).'js/jquery.flot.min.js', array(), '0.8.3', true);
	wp_enqueue_script('flot-axislabels', plugin_dir_url(__FILE__).'js/jquery.flot.axislabels.js', array(), '2.0', true);
	wp_enqueue_script('flot-time', plugin_dir_url(__FILE__).'js/jquery.flot.time.min.js', array(), '0.8.3', true);
	//Add jQuery script
	function shortcode_system_uptime_week_graph_jquery() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function($) {
			//Graph data
			var data = [{
				color: '<?php echo get_option('xmpp_stats_graph_line_color'); ?>',
				label: '<?php _e('Uptime', 'xmpp_stats'); ?>',
				<?php //Datebase data
				global $wpdb;
				$table_name = $wpdb->prefix . 'xmpp_stats';
				//Get latest record
				$row = $wpdb->get_row("SELECT * FROM $table_name WHERE type = '6' ORDER BY timestamp DESC");
				//Calculation oldest date for select
				$oldest = date_i18n('Y-m-d H:i:s', strtotime($row->timestamp)-(7*24*60*60));
				//Get data from the last 24 hours
				$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE type = '6' AND timestamp > '$oldest' ORDER BY timestamp ASC");
				foreach($rows as $row) {
					$timestamp = strtotime($row->timestamp);
					if($row === reset($rows))
						echo 'data: [ ['.$timestamp.'000, '.($row->value/(60*60*24)).'], ';
					else if($row === end($rows))
						echo '['.$timestamp.'000, '.($row->value/(60*60*24)).'] ]'."\n";
					else
						echo '['.$timestamp.'000, '.($row->value/(60*60*24)).'], ';
				} ?>
			}];
			//Graph options
			var options = {
				xaxis: {
					mode: 'time',
					timezone: 'browser',
					tickSize: [1, 'day'],
					timeformat: '%a</br>%e.%m',
					dayNames: ['<?php _e('sun', 'xmpp_stats'); ?>', '<?php _e('mon', 'xmpp_stats'); ?>', '<?php _e('tue', 'xmpp_stats'); ?>', '<?php _e('wed', 'xmpp_stats'); ?>', '<?php _e('thu', 'xmpp_stats'); ?>', '<?php _e('fri', 'xmpp_stats'); ?>', '<?php _e('sat', 'xmpp_stats'); ?>']
				},
				yaxis: {
					tickDecimals: 0
				},
				series: {
					lines: {
						lineWidth: 0,
						fill: true
					},
					shadowSize: 0
				},
				grid: {
					color: '<?php echo get_option('xmpp_stats_graph_grid_color'); ?>',
					borderWidth: 1
				},
				legend: {
					show: false
				}
			};
			//Draw graph
			$.plot('#system_uptime_week_graph', data, options);
			//Redraw graph
			$(window).on("resize", function( event ) {
				$.plot('#system_uptime_week_graph', data, options);
			});
		});
		</script> <?php
	}
	add_action('wp_footer', 'shortcode_system_uptime_week_graph_jquery');
	return '<div class="graph-container"><h3>'.__('System uptime - by week', 'xmpp_stats').'</h3><div id="system_uptime_week_graph" class="graph-placeholder"></div></div>';
}

//Add shortcodes
add_shortcode('xmpp_onlineusers_day_graph', 'shortcode_xmpp_onlineusers_day_graph');
add_shortcode('xmpp_onlineusers_week_graph', 'shortcode_xmpp_onlineusers_week_graph');
add_shortcode('xmpp_registeredusers_day_graph', 'shortcode_xmpp_registeredusers_day_graph');
add_shortcode('xmpp_registeredusers_week_graph', 'shortcode_xmpp_registeredusers_week_graph');
add_shortcode('xmpp_s2s_day_graph', 'shortcode_xmpp_s2s_day_graph');
add_shortcode('xmpp_s2s_week_graph', 'shortcode_xmpp_s2s_week_graph');
add_shortcode('xmpp_uptime_day_graph', 'shortcode_xmpp_uptime_day_graph');
add_shortcode('xmpp_uptime_week_graph', 'shortcode_xmpp_uptime_week_graph');
add_shortcode('system_uptime_day_graph', 'shortcode_system_uptime_day_graph');
add_shortcode('system_uptime_week_graph', 'shortcode_system_uptime_week_graph');
