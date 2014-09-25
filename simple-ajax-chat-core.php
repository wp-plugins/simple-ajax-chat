<?php 
/*
	Plugin Name: Simple Ajax Chat
	Plugin URI: http://perishablepress.com/simple-ajax-chat/
	Description: Displays a fully customizable Ajax-powered chat box anywhere on your site.
	Author: Jeff Starr
	Author URI: http://monzilla.biz/
	Donate link: http://m0n.co/donate
	Version: 20140923
	Stable tag: trunk
	License: GPL v2
	Usage: Visit the plugin's settings page for shortcodes, template tags, and more information.
	Tags: chat, ajax, forum, im
*/

if (!function_exists('add_action')) die();



$sac_options = get_option('sac_options');

$sac_version = '20140923';
$sac_plugin  = 'Simple Ajax Chat';
$sac_path    = 'simple-ajax-chat/simple-ajax-chat-admin.php';
$sac_homeurl = 'http://perishablepress.com/simple-ajax-chat/';

$sac_lastID = isset($_GET['sac_lastID']) ? $_GET['sac_lastID'] : "";

$sac_user_name = isset($_POST['n']) ? $_POST['n'] : ""; 
$sac_user_url  = isset($_POST['u']) ? $_POST['u'] : "";
$sac_user_text = isset($_POST['c']) ? $_POST['c'] : "";

$sacGetChat  = isset($_GET['sacGetChat'])  ? $_GET['sacGetChat']  : "";
$sacSendChat = isset($_GET['sacSendChat']) ? $_GET['sacSendChat'] : "";

$sac_admin_user_level     = 8;
$sac_number_of_comments   = 999;
$sac_number_of_characters = 500;
$sac_username_length      = 20;

require_once(dirname(__FILE__) . '/simple-ajax-chat-admin.php');
require_once(dirname(__FILE__) . '/simple-ajax-chat-form.php');



// i18n
function sac_i18n_init() {
	load_plugin_textdomain('sac', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'sac_i18n_init');

// check WP version
function sac_require_wp_version() {
	global $wp_version, $sac_path, $sac_plugin;
	if (version_compare($wp_version, '3.7', '<')) {
		if (is_plugin_active($sac_path)) {
			deactivate_plugins($sac_path);
			$msg =  '<strong>' . $sac_plugin . '</strong> ' . __('requires WordPress 3.7 or higher, and has been deactivated!', 'sac') . '<br />';
			$msg .= __('Please return to the', 'sac') . ' <a href="' . admin_url() . '">' . __('WordPress Admin area', 'sac') . '</a> ' . __('to upgrade WordPress and try again.', 'sac');
			wp_die($msg);
		}
	}
}
if (isset($_GET['activate']) && $_GET['activate'] == 'true') add_action('admin_init', 'sac_require_wp_version');



// install DB table
function sac_create_table() {
	global $wpdb, $user_level, $sac_admin_user_level;
	if ($user_level < $sac_admin_user_level) return;
	
	$table_name = $wpdb->prefix . 'ajax_chat';
	$check_table = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
	
	if ($check_table != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			id mediumint(7) NOT NULL AUTO_INCREMENT, 
			time bigint(11) DEFAULT '0' NOT NULL, 
			name tinytext NOT NULL, 
			text text NOT NULL, 
			url text NOT NULL, 
			ip text NOT NULL, 
			UNIQUE KEY id (id)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		$welcome_name = "The Admin";
		$welcome_ip   = sac_get_ip_address();
		$welcome_text = __('High five! You&rsquo;ve successfully installed Simple Ajax Chat.', 'sac');
		$wpdb->query("INSERT INTO " . $table_name . " (time, name, text) VALUES ('" . time() . "','" . $welcome_name . "','" . $welcome_text . "')");
	}
}
if (isset($_GET['activate']) && $_GET['activate'] == 'true') add_action('init', 'sac_create_table');

// get chats
function sac_getData($sac_lastID) {
	global $wpdb, $table_prefix;
	$query = $wpdb->get_results("SELECT * FROM " . $table_prefix . "ajax_chat WHERE id > " . $sac_lastID . " ORDER BY id DESC", ARRAY_A);
	$loop = '';
	for ($row = 0; $row < 1; $row++) {
		if (isset($query[$row])){
			if (!is_null($query[$row]) && is_array($query[$row])) { 
				while (list($key, $value) = each($query[$row])) {
					$id   = $query[$row]['id'];
					$time = $query[$row]['time'];
					$name = $query[$row]['name'];
					$text = $query[$row]['text'];
					$url  = $query[$row]['url'];
					$loop = $id . '---' . $name . '---' . $text . '---' . sac_time_since($time) . ' ' . __('ago', 'sac') . '---' . $url . '---';
				}
			}
		}
	}
	echo $loop;
	if (empty($loop)) echo "0"; // if no new data, send one byte to fix a bug where safari gives up w/ no data
}
if ($sacGetChat == "yes" && !empty($sac_lastID) && is_numeric($sac_lastID)) sac_getData($sac_lastID);

// edit chats
function sac_shout_edit() {
	global $wpdb, $table_prefix, $sac_path;
	if (!current_user_can('manage_options')) {
		die();
	} else {
		if (isset($_GET['sac_comment_id'])) {
			$wpdb->query($wpdb->prepare("UPDATE " . $table_prefix . "ajax_chat SET text = '" . esc_sql($_GET['sac_text']) . "' WHERE id = %d", esc_sql($_GET['sac_comment_id'])));
			wp_redirect(admin_url('options-general.php?page=' . $sac_path . '&sac_edit=true'));
		}
	}
}
if (isset($_GET['sac_edit'])) add_action('init', 'sac_shout_edit');

// delete chats
function sac_shout_delete() {
	global $wpdb, $table_prefix, $sac_path;
	if (!current_user_can('manage_options')) {
		die();
	} else {
		if (isset($_GET['sac_comment_id'])) {
			$wpdb->query($wpdb->prepare("DELETE FROM " . $table_prefix . "ajax_chat WHERE id = %d", esc_sql($_GET['sac_comment_id'])));
			wp_redirect(admin_url('options-general.php?page=' . $sac_path . '&sac_delete=true'));
		}
	}
}
if (isset($_GET['sac_delete'])) add_action('init', 'sac_shout_delete');

// truncate chats
function sac_shout_truncate() {
	global $wpdb, $table_prefix, $sac_path, $sac_options;
	if (!current_user_can('manage_options')) {
		die();
	} else {
		$ip = sac_get_ip_address();
		$default_message = $sac_options['sac_default_message'];
		$default_handle  = $sac_options['sac_default_handle'];
		$sac_script_url  = $sac_options['sac_script_url'];
		if ($sac_script_url === '') $sac_script_url = site_url();

		$wpdb->query("TRUNCATE TABLE " . $table_prefix . "ajax_chat");
		$wpdb->query("INSERT INTO " . $table_prefix . "ajax_chat (time, name, text, url, ip) VALUES ('". time() ."','". $default_handle ."','". $default_message ."','". $sac_script_url ."','". $ip ."')");

		$redirect = add_query_arg(array('sac_truncate'=>false, 'sac_truncate_success'=>'true'), admin_url('options-general.php?page=' . $sac_path));
		wp_redirect($redirect);
	}
}
if ((isset($_GET['sac_truncate']))) add_action('init', 'sac_shout_truncate');



// display settings link on plugin page
function sac_plugin_action_links($links) {
	global $sac_path;
	return array_merge(array('settings'=>'<a href="' . site_url() . '/wp-admin/options-general.php?page=' . $sac_path . '">' . __('Settings', 'sac') .'</a>'), $links);
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'sac_plugin_action_links');

// rate plugin link
function add_sac_links($links, $file) {
	if ($file == plugin_basename(__FILE__)) {
		$rate_url = 'http://wordpress.org/support/view/plugin-reviews/' . basename(dirname(__FILE__)) . '?rate=5#postform';
		$links[] = '<a href="' . $rate_url . '" target="_blank" title="' . __('Click Here to Rate and Review this Plugin on WordPress.org', 'sac') . '">' . __('Rate this plugin', 'sac') . '</a>';
	}
	return $links;
}
add_filter('plugin_row_meta', 'add_sac_links', 10, 2);

// include JavaScript
function sac_add_to_head() {
	global $sac_version, $sac_options;
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
add_action('wp_head', 'sac_add_to_head', 999);

// sac shortcode
function sac_happens() {
	ob_start();
	global $user_identity, $user_ID; 
	simple_ajax_chat();
	$sac_happens = ob_get_contents();
	ob_end_clean();
	return $sac_happens;
}
add_shortcode('sac_happens','sac_happens');

// replace characters
function sac_special_chars($s) {
	$s = wp_strip_all_tags($s, true);
	$s = sanitize_text_field($s);
	$s = str_replace("---", "&minus;-&minus;", $s);
	return $s;
}

// get IP address
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
	return sanitize_text_field($ip_address);
}

// time since entry
function sac_time_since($original) {
	$chunks = array( // unix time (seconds)
		array(60 * 60 * 24 * 365 , __('year', 'sac')), 
		array(60 * 60 * 24 * 30 ,  __('month', 'sac')), 
		array(60 * 60 * 24 * 7,    __('week', 'sac')), 
		array(60 * 60 * 24 ,       __('day', 'sac')), 
		array(60 * 60 ,            __('hour', 'sac')), 
		array(60 ,                 __('minute', 'sac')), 
	);
	$original = $original - 10; // fixes bug where $time & $original match
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


