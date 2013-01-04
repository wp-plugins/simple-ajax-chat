<?php 
/*
	Plugin Name: Simple Ajax Chat
	Plugin URI: http://perishablepress.com/simple-ajax-chat/
	Description: Displays a fully customizable Ajax-powered chat box anywhere on your site.
	Author: Jeff Starr
	Author URI: http://monzilla.biz/
	Version: 20130103
	License: GPL v2
	Usage: Visit the plugin's settings page for shortcodes, template tags, and more information.
	Tags: chat, box, ajax, forum
*/

/*
	Simple Ajax Chat is based on "Jalenack's Wordspew" plugin.
	"Shouts" out to Andrew Sutherland for his tremendous work.
*/

// NO EDITING REQUIRED - PLEASE SET PREFERENCES IN THE WP ADMIN!

$sac_plugin  = 'Simple Ajax Chat';
$sac_path    = 'simple-ajax-chat/simple-ajax-chat.php';
$sac_homeurl = 'http://perishablepress.com/simple-ajax-chat/';
$sac_version = '20130103';

$sac_admin_user_level   = 8;
$sac_number_of_comments = 999;

// register globals
$sac_lastID    = isset($_GET['sac_lastID']) ? $_GET['sac_lastID'] : "";
$sac_user_name = isset($_POST['n']) ? $_POST['n'] : ""; 
$sac_user_url  = isset($_POST['u']) ? $_POST['u'] : "";
$sac_user_text = isset($_POST['c']) ? $_POST['c'] : "";

$sacGetChat  = isset($_GET['sacGetChat']) ? $_GET['sacGetChat'] : "";
$sacSendChat = isset($_GET['sacSendChat']) ? $_GET['sacSendChat'] : "";

// require minimum version of WordPress
if (isset($_GET['sac_process'])) {
	
}
if (function_exists('add_action')) {
	add_action('admin_init', 'sac_require_wp_version');
}
function sac_require_wp_version() {
	global $wp_version, $sac_path, $sac_plugin;
	if (version_compare($wp_version, '3.4', '<')) {
		if (is_plugin_active($sac_path)) {
			deactivate_plugins($sac_path);
			$msg =  '<strong>' . $sac_plugin . '</strong> ' . __('requires WordPress 3.4 or higher, and has been deactivated!') . '<br />';
			$msg .= __('Please return to the ') . '<a href="' . admin_url() . '">' . __('WordPress Admin area') . '</a> ' . __('to upgrade WordPress and try again.');
			wp_die($msg);
		}
	}
}

// install the db table
function sac_create_table() {
	global $table_prefix, $wpdb, $user_level, $sac_admin_user_level;
	get_currentuserinfo();
	if ($user_level < $sac_admin_user_level) return;
	$result = mysql_list_tables(DB_NAME);
	$tables = array();
	while ($row = mysql_fetch_row($result)) { $tables[] = $row[0]; }
	if (!in_array($table_prefix . "ajax_chat", $tables)) {
		$first_install = "yes";
	}
	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	dbDelta("CREATE TABLE " . $table_prefix . "ajax_chat (
		id mediumint(7) NOT NULL AUTO_INCREMENT, 
		time bigint(11) DEFAULT '0' NOT NULL, 
		name tinytext NOT NULL, 
		text text NOT NULL, 
		url text NOT NULL, 
		ip text NOT NULL, 
		UNIQUE KEY id (id)
	);");
	if ($first_install == "yes") {
		$welcome_name = "Perishable";
		$welcome_ip   = sac_get_ip_address();
		$welcome_text = "High five! You&rsquo;ve successfully installed Simple Ajax Chat.";
		$wpdb->query("INSERT INTO " . $table_prefix . "ajax_chat (time, name, text) VALUES ('".time()."','".$welcome_name."','".$welcome_text."')");
	}
}
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	add_action('init', 'sac_create_table');
}

// get ip address
function sac_get_ip_address() {
	if (isset($_SERVER)) {
		if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif(isset($_SERVER["HTTP_CLIENT_IP"])) {
			$ip_address = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$ip_address = $_SERVER["REMOTE_ADDR"];
		}
	} else {
		if(getenv('HTTP_X_FORWARDED_FOR')) {
			$ip_address = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('HTTP_CLIENT_IP')) {
			$ip_address = getenv('HTTP_CLIENT_IP');
		} else {
			$ip_address = getenv('REMOTE_ADDR');
		}
	}
	return $ip_address;
}

// time since entry post, takes argument in unix time (seconds)
function sac_time_since($original) {
	$chunks = array(
		array(60 * 60 * 24 * 365 , 'year'), 
		array(60 * 60 * 24 * 30 , 'month'), 
		array(60 * 60 * 24 * 7, 'week'), 
		array(60 * 60 * 24 , 'day'), 
		array(60 * 60 , 'hour'), 
		array(60 , 'minute'), 
	);
	$original = $original - 10; // eliminates bug where $time & $original match
	$today = time(); // current unix time
	$since = $today - $original;

	for ($i = 0, $j = count($chunks); $i < $j; $i++) {
		$seconds = $chunks[$i][0];
		$name    = $chunks[$i][1];
		if (($count = floor($since / $seconds)) != 0) {
			break;
		}
	}
	$print = ($count == 1) ? '1 '.$name : "$count {$name}s";

	if ($i + 1 < $j) {
		$seconds2 = $chunks[$i + 1][0];
		$name2    = $chunks[$i + 1][1];

		if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
			$print .= ($count2 == 1) ? ', 1 ' . $name2 : ", $count2 {$name2}s";
		}
	}
	return $print;
}

// prevent caching
if ($sacGetChat == "yes" || $sacSendChat == "yes") {
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
	header("Last-Modified: ".gmdate( "D, d M Y H:i:s")."GMT"); 
	header("Cache-Control: no-cache, must-revalidate"); 
	header("Pragma: no-cache");
	header("Content-Type: text/html; charset=utf-8");
	if (!$sac_lastID) $sac_lastID = 0;
}
if ($sacGetChat == "yes") {
	sac_getData($sac_lastID);
}

// get data from database
function sac_getData ($sac_lastID) {
	global $table_prefix;
	$html = sac_prepare_file();
	eval($html);
	$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME, $conn);
	$sql = "SELECT * FROM " . $table_prefix . "ajax_chat WHERE id > " . $sac_lastID . " ORDER BY id DESC";
	$results = mysql_query($sql, $conn);
	$loop = "";
	while ($row = mysql_fetch_array($results)) {
		$id   = $row[0];
		$time = $row[1];
		$name = $row[2];
		$text = $row[3];
		$url  = $row[4];
		// "---" is used to separate the fields in the output
		$loop = $id  ."---" . stripslashes($name) . "---" . stripslashes($text) . "---" . sac_time_since($time) . " ago---" . stripslashes($url) . "---" . $loop; 
	}
	echo $loop;
	// if no new data, send one byte to fix a bug where safari gives up w/ no data
	if (empty($loop)) { echo "0"; }
}

// replace characters
function sac_special_chars($s) {
	$s = htmlspecialchars($s, ENT_COMPAT,'UTF-8');
	return str_replace("---", "&minus;-&minus;", $s);
}

// include php JavaScript
function sac_add_to_head() {
	global $sac_version;
	$sac_options = get_option('sac_options');
	$script_url  = $sac_options['sac_script_url'];
	$current_url = trailingslashit('http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	if ($script_url !== '') {
		if ($script_url == $current_url) {
			echo "\t" . '<script type="text/javascript" src="' . plugins_url('resources/sac.php', __FILE__) . '"></script>' . "\n";
		}
	} else {
		echo "\t" . '<script type="text/javascript" src="' . plugins_url('resources/sac.php', __FILE__) . '"></script>' . "\n";
	}
}
if (function_exists('add_action')) {
	add_action('wp_head', 'sac_add_to_head');
}

// submit fails from chat box
if (isset($_POST['sac_no_js'])) {
	if ($_POST['sac_name'] != '' && $_POST['sac_chat'] != '') {
		if (isset($_POST['sac_url'])) {
			$sac_url = $_POST['sac_url'];
		} else {
			$sac_url = '';
		}
		sac_addData($_POST['sac_name'], $_POST['sac_chat'], $sac_url);
		sac_deleteOld();

		setcookie("sacUserName", $_POST['sac_name'], time()+60*60*24*30*3, '/');
		setcookie("sacUrl", $sac_url, time()+60*60*24*30*3, '/');
		header('location: ' . $_SERVER['HTTP_REFERER']);
	} else {
		echo "Name and comment required.";
	}
}

// add data to database
if ($sac_user_name != '' && $sac_user_text != '' && $sacSendChat == "yes") {
	sac_addData($sac_user_name, $sac_user_text, $sac_user_url);
	sac_deleteOld();
	echo "0";
}

// prepare the config file
function sac_prepare_file() {

	error_reporting(0);

	$html = implode('', file("../../../wp-config.php"));
	$html = str_replace ("require_once", "// ", $html);
	$html = str_replace ("<?php", "", $html);

	$html = str_replace ("if ( !defined", "// ", $html);
	$html = str_replace ("define('ABSPA", "// ", $html);
	$html = str_replace ("define('WP_TE", "// ", $html);
	
	$html = str_replace ("define('AUTH", "// ", $html);
	$html = str_replace ("define('SECU", "// ", $html);
	$html = str_replace ("define('LOGG", "// ", $html);
	$html = str_replace ("define('NONC", "// ", $html);
	
	$html = str_replace ("define('DB_CHA", "// ", $html);
	$html = str_replace ("define('DB_COL", "// ", $html);
	$html = str_replace ("define('WPLANG", "// ", $html);
	$html = str_replace ("define('WP_DEB", "// ", $html);
	return $html;
}

// process data
function sac_addData($sac_user_name, $sac_user_text, $sac_user_url) {
	global $table_prefix;

	$sac_user_text = substr($sac_user_text, 0, 500); // truncate message at 500 characters
	$sac_user_name = substr(trim($sac_user_name), 0,18);

	$sac_user_text = sac_special_chars(trim($sac_user_text));
	$sac_user_name = (empty($sac_user_name)) ? "Anonymous" : sac_special_chars($sac_user_name);
	$sac_user_url  = ($sac_user_url == "http://") ? "" : sac_special_chars($sac_user_url);

	$html = sac_prepare_file();
	eval($html);

	$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME, $conn);

	$query = mysql_query("SELECT * FROM " . $table_prefix . "options WHERE option_name LIKE 'sac_censors'", $conn);
	$row   = mysql_fetch_array($query);
	$list  = $row['option_value'];

	$censors = explode(",", strval($list));
	$censors = array_map('trim', $censors);
	if (!empty($censors)) {
		foreach ($censors as $censor) {
			if (!stristr($sac_user_text, $censor) === FALSE) {
				$sac_user_text = str_replace($censor, '', $sac_user_text);
			}
			if (!stristr($sac_user_name, $censor) === FALSE) {
				$sac_user_name = str_replace($censor, '', $sac_user_name);
			}
			if (!stristr($sac_user_url, $censor) === FALSE) {
				$sac_user_url = str_replace($censor, '', $sac_user_url);
			}
		}
	}
	mysql_query("INSERT INTO " . $table_prefix . "ajax_chat (time, name, text, url) VALUES ('" . time() . "','" . 
	mysql_real_escape_string($sac_user_name) . "','" . 
	mysql_real_escape_string($sac_user_text) . "','" . 
	mysql_real_escape_string($sac_user_url)  . "')", $conn);
}

// clean up database
function sac_deleteOld() {
	global $sac_number_of_comments;
	$html = sac_prepare_file();
	eval($html);
	$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME, $conn);
	$results = mysql_query("SELECT * FROM " . $table_prefix . "ajax_chat ORDER BY id DESC LIMIT " . $sac_number_of_comments, $conn);	
	while ($row = mysql_fetch_array($results)) { $id = $row[0]; }
	if ($id) mysql_query("DELETE FROM " . $table_prefix . "ajax_chat WHERE id < " . $id, $conn);
}

// sac shortcode
if (function_exists('add_shortcode')) {
	add_shortcode('sac_happens','sac_happens');
}
function sac_happens() {
	ob_start();
	simple_ajax_chat();
	$sac_happens = ob_get_contents();
	ob_end_clean();
	return $sac_happens;
}

// output form markup
function simple_ajax_chat() {
	global $wpdb, $table_prefix, $user_level, $user_nickname, $user_ID, $sac_path, $sac_user_url, $sac_number_of_comments; 

	$sac_options     = get_option('sac_options');
	$use_url         = $sac_options['sac_use_url'];
	$use_textarea    = $sac_options['sac_use_textarea'];
	$registered_only = $sac_options['sac_registered_only']; 
	$enable_styles   = $sac_options['sac_enable_style'];
	$play_sound      = $sac_options['sac_play_sound'];

	if ($enable_styles) {
		$custom_styles = '<style type="text/css">' . $sac_options['sac_custom_styles'] . '</style>';
	}
	if ($sac_options['sac_content_chat'] !== '') {
		$custom_chat_pre = $sac_options['sac_content_chat'];
	}
	if ($sac_options['sac_content_form'] !== '') {
		$custom_form_pre = $sac_options['sac_content_form'];
	} 
	if ($sac_options['sac_chat_append'] !== '') {
		$custom_chat_app = $sac_options['sac_chat_append'];
	}
	if ($sac_options['sac_form_append'] !== '') {
		$custom_form_app = $sac_options['sac_form_append'];
	} ?>

	<div id="simple-ajax-chat">
		<?php echo $custom_chat_pre; ?>

		<div id="sac-content"></div>
		<div id="sac-output">

	<?php 
	$wpdb->hide_errors();
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table_prefix . "ajax_chat ORDER BY id DESC LIMIT %d", $sac_number_of_comments));
	//$wpdb->show_errors();

	// only add last message <div> if looping for first time
	$sac_first_time = true; 

	if ($results) {
		foreach($results as $r) { 
			$r->text = preg_replace("`(http|ftp)+(s)?:(//)((\w|\.|\-|_)+)(/)?(\S+)?`i", "<a href=\"\\0\" target=\"_blank\" title=\"Open link in new tab\">\\0\</a>", $r->text);
			if ($sac_first_time == true) { ?>

			<div id="sac-latest-message"><span><?php _e("Latest Message:"); ?></span> <em id="responseTime"><?php echo sac_time_since($r->time); ?> ago</em></div>
			<ul id="sac-messages">

			<?php }

			if ($sac_first_time == true) $lastID = $r->id;
			$url = (empty($r->url) && $r->url = "http://") ? $r->name : '<a href="' . $r->url . '" target="_blank">' . $r->name . '</a>'; ?>

					<li><span title="Posted <?php echo sac_time_since($r->time); ?> ago"><?php echo stripslashes($url); ?> : </span> <?php echo convert_smilies(" " . stripslashes($r->text)); ?></li> 

		<?php $sac_first_time = false;
		}
	} else {
		echo "You need <strong>at least one entry</strong> in the chat forum!";
	} ?>

			</ul>
		</div>

		<?php echo $custom_chat_app; ?>




	<?php get_currentuserinfo();
	if ((!$registered_only) || (($registered_only) && ($user_ID))) { 
		echo $custom_form_pre; ?>

		<div id="sac-panel">
			<form id="sac-form" method="post" action="<?php bloginfo('wpurl'); ?>/wp-content/plugins/<?php echo $sac_path; ?>">

				<?php if (!empty($user_nickname)) { ?>

				<fieldset id="sac-user-info">
					<label for="sac_name"><?php _e('Name'); ?>: <span><?php echo $user_nickname ?></span></label>
					<input type="hidden" name="sac_name" id="sac_name" value="<?php echo $user_nickname; ?>" />
					<input type="hidden" name="sac_url" id="sac_url" value="<?php if($use_url) { echo $user_url; } ?>" />
				</fieldset>

				<?php } else { ?>

				<fieldset id="sac-user-info">
					<label for="sac_name"><?php _e('Name'); ?>:</label>
					<input type="text" name="sac_name" id="sac_name" value="<?php if ($_COOKIE['sacUserName']) { echo $_COOKIE['sacUserName']; } ?>" placeholder="Name" />
				</fieldset>

				<?php } if (!$use_url) { echo '<div style="display:none;">'; } ?>

				<fieldset id="sac-user-url">
					<label for="sac_url"><?php _e('URL'); ?>:</label>
					<input type="text" name="sac_url" id="sac_url" value="<?php if ($_COOKIE['sacUrl']) { echo $_COOKIE['sacUrl']; } else { echo 'http://'; } ?>" placeholder="URL" />
				</fieldset>
				<?php if (!$use_url) { echo '</div>'; } ?>

				<fieldset id="sac-user-chat">
					<label for="sac_chat"><?php _e('Message') ?>:</label>

				<?php if ($use_textarea) { ?>

					<textarea name="sac_chat" id="sac_chat" rows="3" onkeypress="return pressedEnter(this,event);" placeholder="Message"></textarea>

				<?php } else { ?>

					<input type="text" name="sac_chat" id="sac_chat" />

				<?php } ?>

				</fieldset>
				<div id="sac-user-submit">
					<input type="submit" id="submitchat" name="submit" class="submit" value="<?php _e('Say it'); ?>" />
					<input type="hidden" id="sac_lastID" value="<?php echo $lastID + 1; ?>" name="sac_lastID" />
					<input type="hidden" name="sac_no_js" value="true" />
					<input type="hidden" name="sac_process" value="true" />
				</div>
			</form>
			<?php if ($play_sound == true) { 
				$res_path = get_bloginfo('wpurl') . '/wp-content/plugins/simple-ajax-chat/resources/'; ?>

				<object id="TheBox" type="application/x-shockwave-flash" data="<?php echo $res_path; ?>player.swf" width="1" height="1" style="visibility:hidden;">
					<param name="movie" value="<?php echo $res_path; ?>player.swf">
					<param name="AllowScriptAccess" value="always">
					<param name="FlashVars" value="listener=myBox">
				</object>
			<?php } ?>

		</div>

		<?php echo $custom_form_app; ?>

	<?php } else { ?>

		<?php echo $custom_form_pre; ?>

		<div id="sac-panel">
			<p>You must be a registered user to participate in this chat.</p>
		</div>

		<?php echo $custom_form_app; ?>

	<?php } ?>

	</div>
	<?php echo $custom_styles; ?>

<?php }

// edit chats from admin
function sac_shout_edit() {
	global $wpdb, $table_prefix, $user_level, $sac_admin_user_level, $sac_path;
	get_currentuserinfo();
	if ($user_level < $sac_admin_user_level) die();
	if (isset($_GET['sac_comment_id'])) {
		$wpdb->query($wpdb->prepare("UPDATE " . $table_prefix . "ajax_chat SET text = '" . $wpdb->escape($_GET['sac_text']) . "' WHERE id = %d", $wpdb->escape($_GET['sac_comment_id'])));
		wp_redirect(admin_url('options-general.php?page=' . $sac_path . '&sac_edit=true'));
	}
}
if (isset($_GET['sac_edit'])) {
	add_action('init', 'sac_shout_edit');
}

// delete chats from admin
function sac_shout_delete() {
	global $wpdb, $table_prefix, $user_level, $sac_admin_user_level, $sac_path;
	get_currentuserinfo();
	if ($user_level < $sac_admin_user_level) die();
	if (isset($_GET['sac_comment_id'])) {
		$wpdb->query($wpdb->prepare("DELETE FROM " . $table_prefix . "ajax_chat WHERE id = %d", $wpdb->escape($_GET['sac_comment_id'])));
		wp_redirect(admin_url('options-general.php?page=' . $sac_path . '&sac_delete=true'));
	}
}
if (isset($_GET['sac_delete'])) {
	add_action('init', 'sac_shout_delete');
}

// truncate chats from admin
function sac_shout_truncate() {
	global $wpdb, $table_prefix, $user_level, $sac_admin_user_level, $sac_path;
	$sac_options = get_option('sac_options');
	$default_message = $sac_options['sac_default_message'];
	$default_handle  = $sac_options['sac_default_handle'];

	get_currentuserinfo();
	if ($user_level < $sac_admin_user_level) die();

	$wpdb->query("TRUNCATE TABLE " . $table_prefix . "ajax_chat");
	$wpdb->query("INSERT INTO " . $table_prefix . "ajax_chat (time, name, text) VALUES ('" . time() . "','" . $default_handle . "','" . $default_message . "')");

	$redirect = add_query_arg(array('sac_truncate'=>false, 'sac_truncate_success'=>'true'), admin_url('options-general.php?page=' . $sac_path));
	wp_redirect($redirect);
}
if ((isset($_GET['sac_truncate'])) || (isset($_GET['killswitch']))) {
	add_action('init', 'sac_shout_truncate');
}

// display settings link on plugin page
if (function_exists('add_filter')) {
	add_filter('plugin_action_links', 'sac_plugin_action_links', 10, 2);
}
function sac_plugin_action_links($links, $file) {
	global $sac_path;
	if ($file == $sac_path) {
		$sac_links = '<a href="' . get_admin_url() . 'options-general.php?page=' . $sac_path . '">' . __('Settings') .'</a>';
		array_unshift($links, $sac_links);
	}
	return $links;
}

// default settings
$sac_default_plugin_options = array(
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
	'sac_default_message' => 'Welcome to the Chat Forum',
	'sac_default_handle'  => 'Simple Ajax Chat',
	'sac_custom_styles'   => 'div#simple-ajax-chat{width:100%;overflow:hidden}div#sac-content{display:none}div#sac-output{float:left;width:58%;height:250px;overflow:auto;border:1px solid #efefef}div#sac-latest-message{padding:5px 10px;background-color:#efefef}ul#sac-messages{margin:0;padding:0;font-size:13px;line-height:16px}ul#sac-messages li{margin:0;padding:3px 3px 3px 10px}ul#sac-messages li span{font-weight:bold}div#sac-panel{float:right;width:40%}form#sac-form fieldset{border:0;}form#sac-form fieldset label,form#sac-form fieldset input,form#sac-form fieldset textarea{float:left;clear:both;width:94%;margin:0 0 5px 2px}form#sac-form fieldset#sac-user-info label,form#sac-form fieldset#sac-user-url label,form#sac-form fieldset#sac-user-chat label{margin:0 0 0 2px}',
	'sac_content_chat'    => '',
	'sac_content_form'    => '',
	'sac_script_url'      => '',
	'sac_chat_append'     => '',
	'sac_form_append'     => '',
	'sac_play_sound'      => true,
);

// delete plugin settings
if (isset($_POST['sac_restore'])) {
	global $sac_default_plugin_options;
	update_option('sac_options', $sac_default_plugin_options);
	update_option('sac_censors', '');
	$fixed_uri = str_replace("options.php", "options-general.php", $_SERVER["REQUEST_URI"]);
	header("Location: http://" . $_SERVER["HTTP_HOST"] . $fixed_uri . "?page=" . $sac_path . "&sac_restore_success=true");
}

// define default settings
if (function_exists('register_activation_hook')) {
	register_activation_hook(__FILE__, 'sac_add_defaults');
}
function sac_add_defaults() {
	global $sac_default_plugin_options;
	$tmp = get_option('sac_options');
	if(!is_array($tmp)) {
		update_option('sac_options', $sac_default_plugin_options);
		update_option('sac_censors', '');
	}
}

// whitelist settings
if (function_exists('add_action')) {
	add_action('admin_init', 'sac_init');
}
function sac_init() {
	register_setting('sac_plugin_options', 'sac_options', 'sac_validate_options');
	register_setting('sac_plugin_options_censors', 'sac_censors', 'sac_validate_options_censors');
}

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

// add the options page
if (function_exists('add_action')) {
	add_action('admin_menu', 'sac_add_options_page');
}
function sac_add_options_page() {
	global $sac_plugin;
	add_options_page($sac_plugin, $sac_plugin, 'manage_options', __FILE__, 'sac_render_form');
}

// create the options page
function sac_render_form() {
	global $wpdb, $sac_plugin, $sac_path, $sac_homeurl, $sac_version, $sac_number_of_comments; 
	$chats = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ajax_chat ORDER BY id DESC LIMIT %d", $sac_number_of_comments)); 

	$chat_report = 'Currently there';
	if (!empty($chats)) {
		if (count($chats) == 1) { 
			$chat_report .= ' is '; 
		} else { 
			$chat_report .= ' are '; 
		}
		$chat_report .= count($chats) . ' chat messag';
		if (count($chats) == 1) { 
			$chat_report .= 'e (your default message)'; 
		} else { 
			$chat_report .= 'es'; 
		}
	} else {
		$chat_report .= '0 chat messages. Please add at least one message via the chat box.';
	} ?>

	<style type="text/css">
		.mm-panel-overview { padding-left: 135px; background: url(<?php echo plugins_url(); ?>/simple-ajax-chat/images/sac-logo.png) no-repeat 15px 0; }

		#mm-plugin-options h2 small { font-size: 60%; }
		#mm-plugin-options h3 { cursor: pointer; }
		#mm-plugin-options h4, 
		#mm-plugin-options p { margin: 15px; line-height: 18px; }
		#mm-plugin-options ul { margin: 15px 15px 25px 40px; }
		#mm-plugin-options li { margin: 10px 0; list-style-type: disc; }
		#mm-plugin-options abbr { cursor: help; border-bottom: 1px dotted #dfdfdf; }

		.mm-table-wrap { margin: 15px; }
		.mm-table-wrap td { padding: 5px 10px; vertical-align: middle; }
		.mm-table-wrap .mm-table {}
		.mm-table-wrap .widefat td { width: 80%; padding: 5px 10px; vertical-align: middle; }
		.mm-table-wrap .widefat th { width: 20%; padding: 10px; vertical-align: middle; }
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
			<p><strong><?php _e('The comment was deleted successfully.'); ?></strong></p>
		</div>
	
		<?php } if (isset($_GET['sac_edit'])) { ?>

		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php _e('The comment was edited successfully.'); ?></strong></p>
		</div>

		<?php } if (isset($_GET['sac_truncate_success'])) { ?>

		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php _e('All chat messages have been cleared from the database.'); ?></strong></p>
		</div>

		<?php } if (isset($_GET['sac_restore_success'])) { ?>

		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php _e('Options successfully restored to default settings.'); ?></strong></p>
		</div>
		
		<?php } ?>

		<div id="mm-panel-toggle"><a href="<?php get_admin_url() . 'options-general.php?page=' . $sac_path; ?>"><?php _e('Toggle all panels'); ?></a></div>
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				<div id="mm-panel-overview" class="postbox">
					<h3><?php _e('Overview'); ?></h3>
					<div class="toggle default-hidden">
						<div class="mm-panel-overview">
							<p>
								<strong><?php echo $sac_plugin; ?></strong> <?php _e('(SAC) displays an Ajax-powered chat box anywhere on your site.'); ?>
								<?php _e('Use the shortcode to display the chat box on a post or page. Use the template tag to display anywhere in your theme template.'); ?>
							</p>
							<ul>
								<li><?php _e('To configure your settings, visit'); ?> <a id="mm-panel-primary-link" href="#mm-panel-primary"><?php _e('Chat Options'); ?></a>.</li>
								<li><?php _e('For the shortcode and template tag, visit'); ?> <a id="mm-panel-secondary-link" href="#mm-panel-secondary"><?php _e('Template Tag &amp; Shortcode'); ?></a>.</li>
								<li><?php _e('To manage the current chat messages, visit'); ?> <a id="mm-panel-tertiary-link" href="#mm-panel-tertiary"><?php _e('Manage Chat Messages'); ?></a>.</li>
								<li><?php _e('To block a word or phrase from chat, visit'); ?> <a id="mm-panel-quaternary-link" href="#mm-panel-quaternary"><?php _e('Banned Phrases'); ?></a>.</li>
								<li><?php _e('For more information check the <code>readme.txt</code> and'); ?> <a href="<?php echo $sac_homeurl; ?>"><?php _e('SAC Homepage'); ?></a>.</li>
							</ul>
						</div>
					</div>
				</div>
				<div id="mm-panel-primary" class="postbox">
					<h3><?php _e('Chat Options'); ?></h3>

					<?php if (isset($_GET["settings-updated"]) || isset($_GET["sac_restore_success"])) {
						$sac_updated_options = true;
					} ?>
					<div class="toggle<?php if (!$sac_updated_options) { echo ' default-hidden'; } ?>">
						<p><?php _e('Here you may customize Simple Ajax Chat to suit your needs. Note: after updating time and color options, you may need to refresh/empty the browser cache before you see the changes take effect.'); ?></p>
						<form method="post" action="options.php">
							<?php $sac_options = get_option('sac_options'); settings_fields('sac_plugin_options'); ?>
							<h4><?php _e('General options'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_registered_only]"><?php _e('Require log in?'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_registered_only]" value="1" <?php if (isset($sac_options['sac_registered_only'])) { checked('1', $sac_options['sac_registered_only']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box to require users to be logged in (i.e., registered users) to use the chat box. If enabled, non-logged-in users will be able to read the chat but not participate.'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_use_url]"><?php _e('Enable URL?'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_use_url]" value="1" <?php if (isset($sac_options['sac_use_url'])) { checked('1', $sac_options['sac_use_url']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box if you want users to be able to include a URL for their chat name.'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_use_textarea]"><?php _e('Use textarea?'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_use_textarea]" value="1" <?php if (isset($sac_options['sac_use_textarea'])) { checked('1', $sac_options['sac_use_textarea']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box to use a larger input field for chat messages.'); ?></span></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_default_handle]"><?php _e('Default name'); ?></label></th>
										<td>
											<input type="text" size="50" maxlength="200" name="sac_options[sac_default_handle]" value="<?php echo $sac_options['sac_default_handle']; ?>" />
											<div class="mm-item-caption"><?php _e('Here you may customize the name of the username for the &ldquo;welcome&rdquo; message. Note: reset/clear the chat messages for the new name to be displayed.'); ?></div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_default_message]"><?php _e('Default message'); ?></label></th>
										<td>
											<input type="text" size="50" maxlength="200" name="sac_options[sac_default_message]" value="<?php echo $sac_options['sac_default_message']; ?>" />
											<div class="mm-item-caption"><?php _e('Here you may customize the &ldquo;welcome&rdquo; message that appears as the first chat comment. Note: reset/clear the chat messages for the new welcome message to be displayed.'); ?></div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_play_sound]"><?php _e('Sound alerts?'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_play_sound]" value="1" <?php if (isset($sac_options['sac_play_sound'])) { checked('1', $sac_options['sac_play_sound']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box if you want to hear a sound for new chat messages. Tip: to change the sound file, replace the file "msg.mp3" with any (short) mp3 file.'); ?></span></td>
									</tr>
								</table>
							</div>
							<h4><?php _e('Times and colors'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_update_seconds]"><?php _e('Update interval'); ?></label></th>
										<td>
											<input type="text" size="5" maxlength="10" name="sac_options[sac_update_seconds]" value="<?php echo $sac_options['sac_update_seconds']; ?>" />
											<div class="mm-item-caption">
												<?php _e('Indicate the refresh frequency (in milliseconds, decimals allowed). Smaller numbers make new chat messages appear faster, but also increase server load.
													This number is used as the interval for the first eight Ajax requests; after that, the number is automatically increased. Adding a new comment or 
													mousing over the chat box will reset the interval to the number specified here. The default is 3 seconds (3000 ms).'); ?>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_fade_length]"><?php _e('Fade duration'); ?></label></th>
										<td>
											<input type="text" size="5" maxlength="10" name="sac_options[sac_fade_length]" value="<?php echo $sac_options['sac_fade_length']; ?>" />
											<div class="mm-item-caption"><?php _e('This number specifies the fade-duration of the most recent chat message (in milliseconds, decimals allowed). Default is 1.5 seconds (1500 ms).'); ?></div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_fade_from]"><?php _e('Highlight fade (from)'); ?></label></th>
										<td>
											<input type="text" size="50" maxlength="200" name="sac_options[sac_fade_from]" value="<?php echo $sac_options['sac_fade_from']; ?>" />
											<div class="mm-item-caption"><?php _e('Here you may customize the &ldquo;fade-in&rdquo; background-color of new chat messages. Note: colors must be 6-digit-hex format, default color is <code>#ffffcc</code>.'); ?></div>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_fade_to]"><?php _e('Highlight fade (to)'); ?></label></th>
										<td>
											<input type="text" size="50" maxlength="200" name="sac_options[sac_fade_to]" value="<?php echo $sac_options['sac_fade_to']; ?>" />
											<div class="mm-item-caption"><?php _e('Here you may customize the &ldquo;fade-out&rdquo; background-color of new chat messages. Note: colors must be 6-digit-hex format, default color is <code>#ffffff</code>.'); ?></div>
										</td>
									</tr>
								</table>
							</div>
							<h4><?php _e('Appearance'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_custom_styles]"><?php _e('Custom CSS styles'); ?></label></th>
										<td><textarea class="textarea" rows="5" cols="50" name="sac_options[sac_custom_styles]"><?php echo esc_textarea($sac_options['sac_custom_styles']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may add custom CSS to style the chat form. Do not include <code>&lt;style&gt;</code> tags. Note: view <code>/resources/sac.css</code> for a complete set of available CSS hooks.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_enable_style]"><?php _e('Enable custom styles?'); ?></label></th>
										<td><input type="checkbox" name="sac_options[sac_enable_style]" value="1" <?php if (isset($sac_options['sac_enable_style'])) { checked('1', $sac_options['sac_enable_style']); } ?> /> 
										<span class="mm-item-caption"><?php _e('Check this box if you want to enable the CSS styles.'); ?></span></td>
									</tr>
								</table>
							</div>
							<h4><?php _e('Targeted loading'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_script_url]"><?php _e('Chat URL'); ?></label></th>
										<td>
											<input type="text" size="50" maxlength="200" name="sac_options[sac_script_url]" value="<?php echo $sac_options['sac_script_url']; ?>" />
											<div class="mm-item-caption"><?php _e('By default, the plugin includes its JavaScript on <em>every</em> page. To prevent this, and to include its JavaScript only on the chat page, enter the URL where it&rsquo;s displayed. Note: leave blank to disable.'); ?></div>
										</td>
									</tr>
								</table>
							</div>
							<h4><?php _e('Custom content'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_content_chat]"><?php _e('Custom content before chat box'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="sac_options[sac_content_chat]"><?php echo esc_textarea($sac_options['sac_content_chat']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may specify any custom text/markup that will appear <strong>before</strong> the chat box. Note: leave blank to disable.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_chat_append]"><?php _e('Custom content after chat box'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="sac_options[sac_chat_append]"><?php echo esc_textarea($sac_options['sac_chat_append']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may specify any custom text/markup that will appear <strong>after</strong> the chat box. Note: leave blank to disable.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_content_form]"><?php _e('Custom content before chat form'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="sac_options[sac_content_form]"><?php echo esc_textarea($sac_options['sac_content_form']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may specify any custom text/markup that will appear <strong>before</strong> the chat form.  Note: leave blank to disable.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="sac_options[sac_form_append]"><?php _e('Custom content after chat form'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="sac_options[sac_form_append]"><?php echo esc_textarea($sac_options['sac_form_append']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may specify any custom text/markup that will appear <strong>after</strong> the chat form.  Note: leave blank to disable.'); ?></div></td>
									</tr>
								</table>
							</div>
							<input type="submit" class="button-primary" value="<?php _e('Save Settings'); ?>" />
						</form>
					</div>
				</div>
				<div id="mm-panel-secondary" class="postbox">
					<h3><?php _e('Template Tag &amp; Shortcode'); ?></h3>
					<div class="toggle default-hidden">
						<h4><?php _e('Shortcode'); ?></h4>
						<p><?php _e('Use this shortcode to display the chat box on a post or page:'); ?></p>
						<p><code class="mm-code">[sac_happens]</code></p>
						<h4><?php _e('Template tag'); ?></h4>
						<p><?php _e('Use this template tag to display the chat box anywhere in your theme template:'); ?></p>
						<p><code class="mm-code">&lt;?php if (function_exists('simple_ajax_chat')) simple_ajax_chat(); ?&gt;</code></p>
					</div>
				</div>
				<div id="mm-panel-tertiary" class="postbox">
					<h3><?php _e('Manage Chat Messages'); ?></h3>
						
					<?php if (isset($_GET["sac_delete"]) || isset($_GET["sac_edit"]) || isset($_GET["sac_truncate_success"])) {
						$sac_updated_message = true;
					} ?>
					<div class="toggle<?php if (!$sac_updated_message) { echo ' default-hidden'; } ?>">
						<p>
							<?php _e('Here is a <em>static</em> list of all chat messages for editing and/or deleting. Note that you must have at least <strong>one message</strong> in the chat box at all times.'); ?> 
							<?php _e('Clicking &ldquo;Delete all chat messages&rdquo; will clear the database and add your default message to make it all good.'); ?>
						</p>
						<h4><?php echo $chat_report; ?></h4>
						<div class="mm-table-wrap">

							<?php if (empty($chats)) { ?>

								<p><strong>You must have at least one message in the chat box at all times! Go post a few chat messages and try again.</strong></p>

							<?php } else {
 
									$sac_first_time = "yes";
									foreach ($chats as $chat) {

										$url = (empty($chat->url) && $chat->url = "http://") ? $chat->name : '<a href="' . $chat->url . '">' . $chat->name . '</a>';
										if ($sac_first_time == "yes") { ?>

										<div><span>Last Message</span> <em><?php echo sac_time_since($chat->time); ?> ago</em></div>
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
					<h3><?php _e('Banned Phrases'); ?></h3>

					<?php if (isset($_GET["settings-updated"]) || isset($_GET["sac_restore_success"])) {
						$sac_updated_list = true;
					} ?>
					<div class="toggle<?php if (!$sac_updated_list) { echo ' default-hidden'; } ?>">
						<p><?php _e('Here you may specify a list of words that should be banned from the chat room. Separate words with commas. Note: this applies to usernames, URLs, and chat messages.'); ?></p>
						<form method="post" action="options.php">
							<?php $sac_censors = get_option('sac_censors'); settings_fields('sac_plugin_options_censors'); ?>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="sac_censors"><?php _e('Banned phrases'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="sac_censors"><?php echo esc_textarea($sac_censors); ?></textarea></td>
									</tr>
								</table>
							</div>
							<input type="submit" class="button-primary" value="<?php _e('Save Settings'); ?>" />
						</form>
					</div>
				</div>
				<div id="mm-restore-settings" class="postbox">
					<h3><?php _e('Restore Default Options'); ?></h3>
					<div class="toggle default-hidden">
						<p><?php _e('Click the button to restore plugin options to their default setttings.'); ?></p>
						<form method="post" action="options.php">
							<input type="submit" class="button-primary" id="mm_restore_defaults" value="<?php _e('Restore default settings'); ?>" />
							<input type="hidden" name="sac_restore" value="Reset" /> 
						</form>
					</div>
				</div>
				<div id="mm-panel-current" class="postbox">
					<h3><?php _e('Updates &amp; Info'); ?></h3>
					<div class="toggle default-hidden">
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
				var r = confirm("<?php _e('Are you sure you want to delete alll chat messages? (this action cannot be undone)'); ?>");
				if (r == true){
					return true;
				} else {
					return false;
				}
			});
			jQuery("#mm_restore_defaults").click(function(){
				var r = confirm("<?php _e('Are you sure you want to restore default settings? (this action cannot be undone)'); ?>");
				if (r == true){
					return true;
				} else {
					return false;
				}
			});
		});
	</script>

<?php } ?>