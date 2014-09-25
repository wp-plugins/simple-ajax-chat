<?php // Simple Ajax Chat > Settings

if (!function_exists('add_action')) die();



// add default settings
function sac_add_defaults() {
	$tmp = get_option('sac_options');
	if (($tmp['default_options'] == '1') || (!is_array($tmp))) {
		$sac_default_censors = 'firstbannedword,secondbannedword,thirdbannedword';
		$sac_default_options = array(
			'default_options'     => 0,
			'sac_fade_from'       => '#ffffcc',
			'sac_fade_to'         => '#ffffff',
			'sac_update_seconds'  => '3000',
			'sac_fade_length'     => '1500',
			'sac_text_color'      => '#777777', // not used
			'sac_name_color'      => '#333333', // not used
			'sac_use_url'         => true,
			'sac_use_textarea'    => true,
			'sac_registered_only' => false,
			'sac_enable_style'    => true,
			'sac_default_message' => __('Welcome to the Chat Forum', 'sac'),
			'sac_default_handle'  => 'Simple Ajax Chat',
			'sac_custom_styles'   => 'div#simple-ajax-chat{width:100%;overflow:hidden}div#sac-content{display:none}div#sac-output{float:left;width:58%;height:250px;overflow:auto;border:1px solid #efefef}div#sac-latest-message{padding:5px 10px;background-color:#efefef}ul#sac-messages{margin:0;padding:0;font-size:13px;line-height:16px}ul#sac-messages li{margin:0;padding:3px 3px 3px 10px}ul#sac-messages li span{font-weight:bold}div#sac-panel{float:right;width:40%}form#sac-form fieldset{border:0;}form#sac-form fieldset label,form#sac-form fieldset input,form#sac-form fieldset textarea{float:left;clear:both;width:94%;margin:0 0 5px 2px}form#sac-form fieldset#sac-user-info label,form#sac-form fieldset#sac-user-url label,form#sac-form fieldset#sac-user-chat label{margin:0 0 0 2px}',
			'sac_content_chat'    => '',
			'sac_content_form'    => '',
			'sac_script_url'      => '',
			'sac_chat_append'     => '',
			'sac_form_append'     => '',
			'sac_play_sound'      => true,
			'sac_chat_order'      => false,
			'sac_logged_name'     => 0,
		);
		update_option('sac_options', $sac_default_options);
		update_option('sac_censors', $sac_default_censors);
	}
}
if (function_exists('register_activation_hook')) register_activation_hook(plugin_dir_path(__FILE__).'simple-ajax-chat-core.php', 'sac_add_defaults');

// reset plugin settings
if (isset($_POST['sac_restore'])) {
	$sac_default_censors = 'firstbannedword,secondbannedword,thirdbannedword';
	$sac_default_options = array(
		'default_options'     => 0,
		'sac_fade_from'       => '#ffffcc',
		'sac_fade_to'         => '#ffffff',
		'sac_update_seconds'  => '3000',
		'sac_fade_length'     => '1500',
		'sac_text_color'      => '#777777', // not used
		'sac_name_color'      => '#333333', // not used
		'sac_use_url'         => true,
		'sac_use_textarea'    => true,
		'sac_registered_only' => false,
		'sac_enable_style'    => true,
		'sac_default_message' => __('Welcome to the Chat Forum', 'sac'),
		'sac_default_handle'  => 'Simple Ajax Chat',
		'sac_custom_styles'   => 'div#simple-ajax-chat{width:100%;overflow:hidden}div#sac-content{display:none}div#sac-output{float:left;width:58%;height:250px;overflow:auto;border:1px solid #efefef}div#sac-latest-message{padding:5px 10px;background-color:#efefef}ul#sac-messages{margin:0;padding:0;font-size:13px;line-height:16px}ul#sac-messages li{margin:0;padding:3px 3px 3px 10px}ul#sac-messages li span{font-weight:bold}div#sac-panel{float:right;width:40%}form#sac-form fieldset{border:0;}form#sac-form fieldset label,form#sac-form fieldset input,form#sac-form fieldset textarea{float:left;clear:both;width:94%;margin:0 0 5px 2px}form#sac-form fieldset#sac-user-info label,form#sac-form fieldset#sac-user-url label,form#sac-form fieldset#sac-user-chat label{margin:0 0 0 2px}',
		'sac_content_chat'    => '',
		'sac_content_form'    => '',
		'sac_script_url'      => '',
		'sac_chat_append'     => '',
		'sac_form_append'     => '',
		'sac_play_sound'      => true,
		'sac_chat_order'      => false,
		'sac_logged_name'     => 0,
	);
	update_option('sac_options', $sac_default_options);
	update_option('sac_censors', $sac_default_censors);
	$fixed_uri = str_replace("options.php", "options-general.php", sanitize_text_field($_SERVER["REQUEST_URI"]));
	header("Location: http://" . sanitize_text_field($_SERVER["HTTP_HOST"]) . $fixed_uri . "?page=" . $sac_path . "&sac_restore_success=true");
}



// whitelist settings
function sac_init() {
	register_setting('sac_plugin_options', 'sac_options', 'sac_validate_options');
	register_setting('sac_plugin_options_censors', 'sac_censors', 'sac_validate_options_censors');
}
if (function_exists('add_action')) add_action('admin_init', 'sac_init');

// sanitize and validate input
function sac_validate_options($input) {

	if (!isset($input['default_options'])) $input['default_options'] = null;
	$input['default_options'] = ($input['default_options'] == 1 ? 1 : 0);

	if (!isset($input['sac_use_url'])) $input['sac_use_url'] = null;
	$input['sac_use_url'] = ($input['sac_use_url'] == 1 ? 1 : 0);

	if (!isset($input['sac_use_textarea'])) $input['sac_use_textarea'] = null;
	$input['sac_use_textarea'] = ($input['sac_use_textarea'] == 1 ? 1 : 0);

	if (!isset($input['sac_registered_only'])) $input['sac_registered_only'] = null;
	$input['sac_registered_only'] = ($input['sac_registered_only'] == 1 ? 1 : 0);

	if (!isset($input['sac_enable_style'])) $input['sac_enable_style'] = null;
	$input['sac_enable_style'] = ($input['sac_enable_style'] == 1 ? 1 : 0);

	if (!isset($input['sac_play_sound'])) $input['sac_play_sound'] = null;
	$input['sac_play_sound'] = ($input['sac_play_sound'] == 1 ? 1 : 0);

	if (!isset($input['sac_chat_order'])) $input['sac_chat_order'] = null;
	$input['sac_chat_order'] = ($input['sac_chat_order'] == 1 ? 1 : 0);

	if (!isset($input['sac_logged_name'])) $input['sac_logged_name'] = null;
	$input['sac_logged_name'] = ($input['sac_logged_name'] == 1 ? 1 : 0);

	$input['sac_update_seconds']  = wp_filter_nohtml_kses($input['sac_update_seconds']);
	$input['sac_fade_length']     = wp_filter_nohtml_kses($input['sac_fade_length']);
	$input['sac_fade_from']       = wp_filter_nohtml_kses($input['sac_fade_from']);
	$input['sac_fade_to']         = wp_filter_nohtml_kses($input['sac_fade_to']);
	$input['sac_text_color']      = wp_filter_nohtml_kses($input['sac_text_color']);
	$input['sac_name_color']      = wp_filter_nohtml_kses($input['sac_name_color']);
	$input['sac_default_message'] = wp_filter_nohtml_kses($input['sac_default_message']);
	$input['sac_default_handle']  = wp_filter_nohtml_kses($input['sac_default_handle']);
	$input['sac_custom_styles']   = wp_filter_nohtml_kses($input['sac_custom_styles']);
	$input['sac_script_url']      = wp_filter_nohtml_kses($input['sac_script_url']);

	// dealing with kses
	global $allowedposttags;
	$allowed_atts = array('align'=>array(), 'class'=>array(), 'id'=>array(), 'dir'=>array(), 'lang'=>array(), 'style'=>array(), 'xml:lang'=>array(), 'src'=>array(), 'alt'=>array(), 'href'=>array(), 'title'=>array());

	$allowedposttags['strong'] = $allowed_atts;
	$allowedposttags['small'] = $allowed_atts;
	$allowedposttags['span'] = $allowed_atts;
	$allowedposttags['abbr'] = $allowed_atts;
	$allowedposttags['code'] = $allowed_atts;
	$allowedposttags['div'] = $allowed_atts;
	$allowedposttags['img'] = $allowed_atts;
	$allowedposttags['h1'] = $allowed_atts;
	$allowedposttags['h2'] = $allowed_atts;
	$allowedposttags['h3'] = $allowed_atts;
	$allowedposttags['h4'] = $allowed_atts;
	$allowedposttags['h5'] = $allowed_atts;
	$allowedposttags['ol'] = $allowed_atts;
	$allowedposttags['ul'] = $allowed_atts;
	$allowedposttags['li'] = $allowed_atts;
	$allowedposttags['em'] = $allowed_atts;
	$allowedposttags['p'] = $allowed_atts;
	$allowedposttags['a'] = $allowed_atts;

	$input['sac_content_chat'] = wp_kses_post($input['sac_content_chat'], $allowedposttags);
	$input['sac_content_form'] = wp_kses_post($input['sac_content_form'], $allowedposttags);
	$input['sac_chat_append'] = wp_kses_post($input['sac_chat_append'], $allowedposttags);
	$input['sac_form_append'] = wp_kses_post($input['sac_form_append'], $allowedposttags);

	return $input;
}
function sac_validate_options_censors($input) {
	$input['sac_censors'] = wp_filter_nohtml_kses($input['sac_censors']);
	return $input;
}

// add options page
function sac_add_options_page() {
	global $sac_plugin;
	add_options_page($sac_plugin, $sac_plugin, 'manage_options', __FILE__, 'sac_render_form');
}
if (function_exists('add_action')) add_action('admin_menu', 'sac_add_options_page');

// render options page
function sac_render_form() {
	global $wpdb, $sac_plugin, $sac_path, $sac_homeurl, $sac_version, $sac_number_of_comments; 
	
	$chats = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ajax_chat ORDER BY id DESC LIMIT %d", $sac_number_of_comments)); 
	// $total = $wpdb->get_var($wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "ajax_chat ORDER BY id DESC LIMIT 1")); // get message count (alt)
	
	$chat_report = 'Currently there';
	
	if (!empty($chats)) {
		if (count($chats) == 1) { 
			$chat_report .= __(' is ', 'sac'); 
		} else { 
			$chat_report .= __(' are ', 'sac'); 
		}
		$chat_report .= count($chats) . __(' chat message', 'sac');
		if (count($chats) == 1) { 
			$chat_report .= __(' (your default message)', 'sac'); 
		} else { 
			$chat_report .= __('s', 'sac'); 
		}
	} else {
		$chat_report .= __('0 chat messages. Please add at least one message via the chat box.', 'sac');
	} ?>

	<style type="text/css">
		.mm-panel-overview { padding-left: 135px; background: url(<?php echo plugins_url(); ?>/simple-ajax-chat/resources/sac-logo.png) no-repeat 15px 0; }

		#mm-plugin-options h2 small { font-size: 60%; }
		#mm-plugin-options h3 { cursor: pointer; }
		#mm-plugin-options h4, 
		#mm-plugin-options p { margin: 15px; line-height: 18px; }
		#mm-plugin-options ul { margin: 15px 15px 25px 40px; }
		#mm-plugin-options li { margin: 10px 0; list-style-type: disc; }
		#mm-plugin-options abbr { cursor: help; border-bottom: 1px dotted #dfdfdf; }
		#mm-plugin-options hr { margin-left: 15px; margin-right: 15px; }

		.mm-table-wrap { margin: 15px; }
		.mm-table-wrap td,
		.mm-table-wrap th { padding: 15px; vertical-align: middle; }
		.mm-table-wrap .widefat th { width: 20%; }
		.mm-item-caption { margin: 3px 0 0 3px; font-size: 11px; line-height: 18px; color: #555; }
		.mm-code { background-color: #fafae0; color: #333; font-size: 14px; }

		#setting-error-settings_updated { margin: 10px 0; }
		#setting-error-settings_updated p { margin: 5px; }
		#mm-plugin-options .button-primary, #mm-plugin-options .button-secondary { margin: 0 0 15px 15px; }

		#mm-plugin-options #mm-chat-list { margin-left: 15px; }
		#mm-chat-list li { width: 100%; overflow: hidden; list-style-type: none; }
		.mm-chat-url { float: left; width: 15%; margin-top: 7px; }
		.mm-chat-text { float: left; width: 80%; }

		#mm-panel-toggle { margin: 5px 0; }
		#mm-credit-info { margin-top: -5px; }
		#mm-iframe-wrap { width: 100%; height: 250px; overflow: hidden; }
		#mm-iframe-wrap iframe { width: 100%; height: 100%; overflow: hidden; margin: 0; padding: 0; }
	</style>

	<div id="mm-plugin-options" class="wrap">
		<?php screen_icon(); ?>

		<h2><?php echo $sac_plugin; ?> <small><?php echo 'v' . $sac_version; ?></small></h2>
		<?php if (isset($_GET['sac_delete'])) { ?>
		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php _e('The comment was deleted successfully.', 'sac'); ?></strong></p>
		</div>
		<?php } if (isset($_GET['sac_edit'])) { ?>
		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php _e('The comment was edited successfully.', 'sac'); ?></strong></p>
		</div>
		<?php } if (isset($_GET['sac_truncate_success'])) { ?>
		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php _e('All chat messages have been cleared from the database.', 'sac'); ?></strong></p>
		</div>
		<?php } if (isset($_GET['sac_restore_success'])) { ?>
		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php _e('Options successfully restored to default settings.', 'sac'); ?></strong></p>
		</div>
		<?php } if (isset($_GET["sac_delete_options"])) { ?>
		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php _e('Options saved. All plugin options will be removed upon uninstall.', 'sac'); ?></strong></p>
		</div>
		<?php } ?>

		<div id="mm-panel-toggle"><a href="<?php get_admin_url() . 'options-general.php?page=' . $sac_path; ?>"><?php _e('Toggle all panels', 'sac'); ?></a></div>
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
	
				<div id="mm-panel-overview" class="postbox">
					<h3><?php _e('Overview', 'sac'); ?></h3>
					<div class="toggle">
						<div class="mm-panel-overview">
							<p>
								<strong><?php echo $sac_plugin; ?></strong> <?php _e('(SAC) displays an Ajax-powered chat box anywhere on your site.', 'sac'); ?>
								<?php _e('Use the shortcode to display the chat box on a post or page. Use the template tag to display anywhere in your theme template.', 'sac'); ?>
							</p>
							<ul>
								<li><?php _e('To configure your settings, visit', 'sac'); ?> <a id="mm-panel-primary-link" href="#mm-panel-primary"><?php _e('Chat Options', 'sac'); ?></a>.</li>
								<li><?php _e('For the shortcode and template tag, visit', 'sac'); ?> <a id="mm-panel-secondary-link" href="#mm-panel-secondary"><?php _e('Template Tag &amp; Shortcode', 'sac'); ?></a>.</li>
								<li><?php _e('To manage the current chat messages, visit', 'sac'); ?> <a id="mm-panel-tertiary-link" href="#mm-panel-tertiary"><?php _e('Manage Chat Messages', 'sac'); ?></a>.</li>
								<li><?php _e('To block a word or phrase from chat, visit', 'sac'); ?> <a id="mm-panel-quaternary-link" href="#mm-panel-quaternary"><?php _e('Banned Phrases', 'sac'); ?></a>.</li>
								<li><?php _e('For more information check the', 'sac'); ?> <a href="<?php echo plugins_url(); ?>/simple-ajax-chat/readme.txt">readme.txt</a> 
									<?php _e('and', 'sac'); ?> <a href="<?php echo $sac_homeurl; ?>"><?php _e('SAC Homepage', 'sac'); ?></a>.</li>
								<li><?php _e('If you like this plugin, please', 'sac'); ?> 
									<a href="http://wordpress.org/support/view/plugin-reviews/<?php echo basename(dirname(__FILE__)); ?>?rate=5#postform" title="<?php _e('Click here to rate and review this plugin on WordPress.org', 'sac'); ?>" target="_blank">
										<?php _e('rate it at the Plugin Directory', 'sac'); ?>&nbsp;&raquo;
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<div id="mm-panel-primary" class="postbox">
					<h3><?php _e('Chat Options', 'sac'); ?></h3>

					<?php if (isset($_GET["settings-updated"]) || isset($_GET["sac_restore_success"])) {
						$sac_updated_options = true;
					} else {
						$sac_updated_options = false;
					} ?>

					<div class="toggle<?php if (!$sac_updated_options) { echo ' default-hidden'; } ?>">
						<p><?php _e('Here you may customize Simple Ajax Chat to suit your needs. Note: after updating time and color options, you may need to refresh/empty the browser cache before you see the changes take effect.', 'sac'); ?></p>
						<form method="post" action="options.php">
							<?php $sac_options = get_option('sac_options'); settings_fields('sac_plugin_options'); ?>
							<h4><?php _e('General options', 'sac'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_chat_order]"><?php _e('Chat order', 'sac'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_chat_order]" value="1" <?php if (isset($sac_options['sac_chat_order'])) { checked('1', $sac_options['sac_chat_order']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box to display chats in ascending order (new messages appear at the bottom of the list). Note: this new feature is experimental and requires jQuery to work correctly. Default: unchecked (new messages appear at the top of the list).', 'sac'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_registered_only]"><?php _e('Require log in?', 'sac'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_registered_only]" value="1" <?php if (isset($sac_options['sac_registered_only'])) { checked('1', $sac_options['sac_registered_only']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box to require users to be logged in (i.e., registered users) to view and use the chat box.', 'sac'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_use_url]"><?php _e('Enable URL?', 'sac'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_use_url]" value="1" <?php if (isset($sac_options['sac_use_url'])) { checked('1', $sac_options['sac_use_url']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box if you want users to be able to include a URL for their chat name.', 'sac'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_use_textarea]"><?php _e('Use textarea?', 'sac'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_use_textarea]" value="1" <?php if (isset($sac_options['sac_use_textarea'])) { checked('1', $sac_options['sac_use_textarea']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box to use a larger input field for chat messages.', 'sac'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_default_handle]"><?php _e('Default name', 'sac'); ?></label></th>
										<td>
											<input type="text" size="50" maxlength="200" name="sac_options[sac_default_handle]" value="<?php echo $sac_options['sac_default_handle']; ?>" />
											<div class="mm-item-caption"><?php _e('Here you may customize the name of the username for the &ldquo;welcome&rdquo; message. Note: reset/clear the chat messages for the new name to be displayed.', 'sac'); ?></div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_default_message]"><?php _e('Default message', 'sac'); ?></label></th>
										<td>
											<input type="text" size="50" maxlength="200" name="sac_options[sac_default_message]" value="<?php echo $sac_options['sac_default_message']; ?>" />
											<div class="mm-item-caption"><?php _e('Here you may customize the &ldquo;welcome&rdquo; message that appears as the first chat comment. Note: reset/clear the chat messages for the new welcome message to be displayed.', 'sac'); ?></div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_play_sound]"><?php _e('Sound alerts?', 'sac'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_play_sound]" value="1" <?php if (isset($sac_options['sac_play_sound'])) { checked('1', $sac_options['sac_play_sound']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box if you want to hear a sound for new chat messages. Tip: to change the sound file, replace the file "msg.mp3" with any (short) mp3 file.', 'sac'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_logged_name]"><?php _e('Use logged-in username', 'sac'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_logged_name]" value="1" <?php if (isset($sac_options['sac_logged_name'])) { checked('1', $sac_options['sac_logged_name']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box if you want to use the logged-in username as the chat name.', 'sac'); ?></span></td>
									</tr>
								</table>
							</div>
							<h4><?php _e('Times and colors', 'sac'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_update_seconds]"><?php _e('Update interval', 'sac'); ?></label></th>
										<td>
											<input type="text" size="5" maxlength="10" name="sac_options[sac_update_seconds]" value="<?php echo $sac_options['sac_update_seconds']; ?>" />
											<div class="mm-item-caption">
												<?php _e('Indicate the refresh frequency (in milliseconds, decimals allowed). Smaller numbers make new chat messages appear faster, but also increase server load.
													This number is used as the interval for the first eight Ajax requests; after that, the number is automatically increased. Adding a new comment or 
													mousing over the chat box will reset the interval to the number specified here. The default is 3 seconds (3000 ms).', 'sac'); ?>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_fade_length]"><?php _e('Fade duration', 'sac'); ?></label></th>
										<td>
											<input type="text" size="5" maxlength="10" name="sac_options[sac_fade_length]" value="<?php echo $sac_options['sac_fade_length']; ?>" />
											<div class="mm-item-caption"><?php _e('This number specifies the fade-duration of the most recent chat message (in milliseconds, decimals allowed). Default is 1.5 seconds (1500 ms).', 'sac'); ?></div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_fade_from]"><?php _e('Highlight fade (from)', 'sac'); ?></label></th>
										<td>
											<input type="text" size="50" maxlength="200" name="sac_options[sac_fade_from]" value="<?php echo $sac_options['sac_fade_from']; ?>" />
											<div class="mm-item-caption"><?php _e('Here you may customize the &ldquo;fade-in&rdquo; background-color of new chat messages. Note: colors must be 6-digit-hex format, default color is <code>#ffffcc</code>.', 'sac'); ?></div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_fade_to]"><?php _e('Highlight fade (to)', 'sac'); ?></label></th>
										<td>
											<input type="text" size="50" maxlength="200" name="sac_options[sac_fade_to]" value="<?php echo $sac_options['sac_fade_to']; ?>" />
											<div class="mm-item-caption"><?php _e('Here you may customize the &ldquo;fade-out&rdquo; background-color of new chat messages. Note: colors must be 6-digit-hex format, default color is <code>#ffffff</code>.', 'sac'); ?></div>
										</td>
									</tr>
								</table>
							</div>
							<h4><?php _e('Appearance', 'sac'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_custom_styles]"><?php _e('Custom CSS styles', 'sac'); ?></label></th>
										<td><textarea class="textarea" rows="5" cols="50" name="sac_options[sac_custom_styles]"><?php echo esc_textarea($sac_options['sac_custom_styles']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may add custom CSS to style the chat form. Do not include <code>&lt;style&gt;</code> tags. Note: view <code>/resources/sac.css</code> for a complete set of available CSS hooks.', 'sac'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_enable_style]"><?php _e('Enable custom styles?', 'sac'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_enable_style]" value="1" <?php if (isset($sac_options['sac_enable_style'])) { checked('1', $sac_options['sac_enable_style']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box if you want to enable the CSS styles.', 'sac'); ?></span></td>
									</tr>
								</table>
							</div>
							<h4><?php _e('Targeted loading', 'sac'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_script_url]"><?php _e('Chat URL', 'sac'); ?></label></th>
										<td>
											<input type="text" size="50" maxlength="200" name="sac_options[sac_script_url]" value="<?php echo $sac_options['sac_script_url']; ?>" />
											<div class="mm-item-caption"><?php _e('By default, the plugin includes its JavaScript on <em>every</em> page. To prevent this, and to include its JavaScript only on the chat page, enter the URL where it&rsquo;s displayed. Note: leave blank to disable.', 'sac'); ?></div>
										</td>
									</tr>
								</table>
							</div>
							<h4><?php _e('Custom content', 'sac'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_content_chat]"><?php _e('Custom content before chat box', 'sac'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="sac_options[sac_content_chat]"><?php echo esc_textarea($sac_options['sac_content_chat']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may specify any custom text/markup that will appear <strong>before</strong> the chat box. Note: leave blank to disable.', 'sac'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_chat_append]"><?php _e('Custom content after chat box', 'sac'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="sac_options[sac_chat_append]"><?php echo esc_textarea($sac_options['sac_chat_append']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may specify any custom text/markup that will appear <strong>after</strong> the chat box. Note: leave blank to disable.', 'sac'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_content_form]"><?php _e('Custom content before chat form', 'sac'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="sac_options[sac_content_form]"><?php echo esc_textarea($sac_options['sac_content_form']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may specify any custom text/markup that will appear <strong>before</strong> the chat form.  Note: leave blank to disable.', 'sac'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_form_append]"><?php _e('Custom content after chat form', 'sac'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="sac_options[sac_form_append]"><?php echo esc_textarea($sac_options['sac_form_append']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may specify any custom text/markup that will appear <strong>after</strong> the chat form.  Note: leave blank to disable.', 'sac'); ?></div></td>
									</tr>
								</table>
							</div>
							<input type="submit" class="button-primary" value="<?php _e('Save Settings', 'sac'); ?>" />
							<!-- maybe use these in a future update -->
							<input type="hidden" name="sac_options[sac_text_color]" value="#777777" />
							<input type="hidden" name="sac_options[sac_name_color]" value="#333333" />
						</form>
					</div>
				</div>

				<div id="mm-panel-secondary" class="postbox">
					<h3><?php _e('Template Tag &amp; Shortcode', 'sac'); ?></h3>
					<div class="toggle default-hidden">
						<h4><?php _e('Shortcode', 'sac'); ?></h4>
						<p><?php _e('Use this shortcode to display the chat box on a post or page:', 'sac'); ?></p>
						<p><code class="mm-code">[sac_happens]</code></p>
						<h4><?php _e('Template tag', 'sac'); ?></h4>
						<p><?php _e('Use this template tag to display the chat box anywhere in your theme template:', 'sac'); ?></p>
						<p><code class="mm-code">&lt;?php if (function_exists('simple_ajax_chat')) simple_ajax_chat(); ?&gt;</code></p>
					</div>
				</div>

				<div id="mm-panel-tertiary" class="postbox">
					<h3><?php _e('Manage Chat Messages', 'sac'); ?></h3>
						
					<?php if (isset($_GET["sac_delete"]) || isset($_GET["sac_edit"]) || isset($_GET["sac_truncate_success"])) {
						$sac_updated_message = true;
					} else {
						$sac_updated_message = false;	
					} ?>
					<div class="toggle<?php if (!$sac_updated_message) { echo ' default-hidden'; } ?>">
						<p>
							<?php _e('Here is a <em>static</em> list of all chat messages for editing and/or deleting. Note that you must have at least <strong>one message</strong> in the chat box at all times.', 'sac'); ?> 
							<?php _e('Clicking &ldquo;Delete all chat messages&rdquo; will clear the database and add your default message to make it all good.', 'sac'); ?>
						</p>
						<h4><?php echo $chat_report; ?></h4>
						<div class="mm-table-wrap">

							<?php if (empty($chats)) { ?>

								<p><strong><?php _e('You must have at least one message in the chat box at all times! Go post a few chat messages and try again.', 'sac'); ?></strong></p>

							<?php } else {
 
									$sac_first_time = "yes";
									foreach ($chats as $chat) {

										$url = (empty($chat->url) && $chat->url = "http://") ? $chat->name : '<a href="' . $chat->url . '">' . $chat->name . '</a>';
										if ($sac_first_time == "yes") { ?>

										<div><span><?php _e('Last Message', 'sac'); ?></span> <em><?php echo sac_time_since($chat->time) . ' ' . __('ago', 'sac'); ?></em></div>
										<ul id="mm-chat-list">
										<?php } ?>

											<li>
												<form name="chat_box_options" action="options.php" method="get">
													<span class="mm-chat-url"><?php echo stripslashes($url); ?>&nbsp;:&nbsp;</span> 
													<span class="mm-chat-text">
														<input type="text" name="sac_text" value="<?php echo stripslashes($chat->text); ?>" size="50" /> 
														<input type="hidden" name="sac_comment_id" value="<?php echo $chat->id; ?>" /> 
														<input type="submit" name="sac_delete" value="Delete" /> 
														<input type="submit" name="sac_edit" value="Edit" /> 
													</span>
												</form>
											</li>
										<?php $sac_first_time = "0";
									} ?>
										</ul>
							<?php } ?>

						</div>
						<form method="get" action="options.php">
							<input type="submit" name="sac_truncate" class="button-secondary" id="mm_truncate_all" value="Delete all chat messages" />
						</form>
					</div>
				</div>

				<div id="mm-panel-quaternary" class="postbox">
					<h3><?php _e('Banned Phrases', 'sac'); ?></h3>

					<?php if (isset($_GET["settings-updated"]) || isset($_GET["sac_restore_success"])) {
						$sac_updated_list = true;
					} else {
						$sac_updated_list = false;
					} ?>
					<div class="toggle<?php if (!$sac_updated_list) { echo ' default-hidden'; } ?>">
						<p><?php _e('Here you may specify a list of words that should be banned from the chat room. Separate words with commas. Note: this applies to usernames, URLs, and chat messages.', 'sac'); ?></p>
						<form method="post" action="options.php">
							<?php $sac_censors = get_option('sac_censors'); settings_fields('sac_plugin_options_censors'); ?>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_censors"><?php _e('Banned phrases', 'sac'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="sac_censors"><?php echo esc_textarea($sac_censors); ?></textarea></td>
									</tr>
								</table>
							</div>
							<input type="submit" class="button-primary" value="<?php _e('Save Settings', 'sac'); ?>" />
						</form>
					</div>
				</div>

				<div id="mm-restore-settings" class="postbox">
					<h3><?php _e('Restore Default Options', 'sac'); ?></h3>
					<?php if (isset($_GET["sac_restore_success"])) {
						$sac_restore_options = true;
					} else {
						$sac_restore_options = false;
					} ?>
					<div class="toggle<?php if (!$sac_restore_options) { echo ' default-hidden'; } ?>">
						<p><strong>Restore default settings</strong></p>
						<p><?php _e('Click the button to restore plugin options to their default setttings.', 'sac'); ?></p>
						<form method="post" action="options.php">
							<input type="submit" class="button-primary" id="mm_restore_defaults" value="<?php _e('Restore default settings', 'sac'); ?>" />
							<input type="hidden" name="sac_restore" value="Reset" /> 
						</form>
						<hr />
						<p><strong>Delete all plugin settings</strong></p>
						<p><?php _e('Note: the plugin&rsquo;s database table and options will be deleted automatically upon uninstalling (deleting) the plugin.', 'sac'); ?></p>
					</div>
				</div>

				<div id="mm-panel-current" class="postbox">
					<h3><?php _e('Updates &amp; Info', 'sac'); ?></h3>
					<div class="toggle">
						<div id="mm-iframe-wrap">
							<iframe src="http://perishablepress.com/current/index-sac.html"></iframe>
						</div>
					</div>
				</div>

			</div>
		</div>
		<div id="mm-credit-info">
			<a target="_blank" href="<?php echo $sac_homeurl; ?>" title="<?php echo $sac_plugin; ?> Homepage"><?php echo $sac_plugin; ?></a> by 
			<a target="_blank" href="http://twitter.com/perishable" title="Jeff Starr on Twitter">Jeff Starr</a> @ 
			<a target="_blank" href="http://monzilla.biz/" title="Obsessive Web Design &amp; Development">Monzilla Media</a>
		</div>
	</div>

	<script type="text/javascript">
		jQuery(document).ready(function(){
			// toggle panels
			jQuery('.default-hidden').hide();
			jQuery('#mm-panel-toggle a').click(function(){
				jQuery('.toggle').slideToggle(300);
				return false;
			});
			jQuery('h3').click(function(){
				jQuery(this).next().slideToggle(300);
			});
			jQuery('#mm-panel-primary-link').click(function(){
				jQuery('.toggle').hide();
				jQuery('#mm-panel-primary .toggle').slideToggle(300);
				return true;
			});
			jQuery('#mm-panel-secondary-link').click(function(){
				jQuery('.toggle').hide();
				jQuery('#mm-panel-secondary .toggle').slideToggle(300);
				return true;
			});
			jQuery('#mm-panel-tertiary-link').click(function(){
				jQuery('.toggle').hide();
				jQuery('#mm-panel-tertiary .toggle').slideToggle(300);
				return true;
			});
			jQuery('#mm-panel-quaternary-link').click(function(){
				jQuery('.toggle').hide();
				jQuery('#mm-panel-quaternary .toggle').slideToggle(300);
				return true;
			});
			// prevent accidents
			jQuery("#mm_truncate_all").click(function(){
				var r = confirm("<?php _e('Are you sure you want to delete alll chat messages? (this action cannot be undone)', 'sac'); ?>");
				if (r == true){
					return true;
				} else {
					return false;
				}
			});
			jQuery("#mm_restore_defaults").click(function(){
				var r = confirm("<?php _e('Are you sure you want to restore default settings? (this action cannot be undone)', 'sac'); ?>");
				if (r == true){
					return true;
				} else {
					return false;
				}
			});
		});
	</script>

<?php }


