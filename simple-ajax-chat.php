<?php // Simple Ajax Chat > Ajax

define('WP_USE_THEMES', false);
require('../../../wp-blog-header.php');
$sac_options = get_option('sac_options');
if (!function_exists('add_action')) die();

//$wpdb->show_errors();
$wpdb->hide_errors();
error_reporting(0);

$sac_host    = ''; $sac_request = ''; $sac_referer = ''; $sac_address = '';
if (isset($_SERVER["HTTP_HOST"]))    $sac_host    = sanitize_text_field($_SERVER["HTTP_HOST"]);
if (isset($_SERVER["REQUEST_URI"]))  $sac_request = sanitize_text_field($_SERVER["REQUEST_URI"]);
if (isset($_SERVER['HTTP_REFERER'])) $sac_referer = sanitize_text_field($_SERVER['HTTP_REFERER']);
if (isset($_SERVER['REMOTE_ADDR']))  $sac_address = sanitize_text_field($_SERVER['REMOTE_ADDR']);

$sac_chat_url = 'http://' . $sac_host . $sac_request;
$sac_match = preg_match("/$sac_host/i", $sac_referer);

// check registered
$registered_only = $sac_options['sac_registered_only'];

// check session
if ($_COOKIE['PHPSESSID'] == session_id()) {
	// legit user
} else {
	die('Please do not load this page directly. Thanks!');
}
session_unset();

// add data to DB
if ((isset($sac_match)) && ($sac_match !== null) && ($sac_match !== 0) && ($sac_match !== '')) {
	if ((isset($sac_referer)) && ($sac_referer !== null) && ($sac_referer !== '')) {
		if (empty($_POST['sac_verify'])) {

			// >
			if (!current_user_can('read') && $registered_only) {
				die ('Please do not load this page directly. Thanks!');

			} else {
				if ($sac_user_name != '' && $sac_user_text != '' && $sacSendChat == "yes") {
					sac_addData($sac_user_name, $sac_user_text, $sac_user_url);
					sac_deleteOld();
					echo "0";
				}
			}
			// >

		}
	}
}

// chat submit fails
if ((isset($sac_match)) && ($sac_match !== null) && ($sac_match !== 0) && ($sac_match !== '')) {
	if ((isset($sac_referer)) && ($sac_referer !== null) && ($sac_referer !== '')) {
		if (empty($_POST['sac_verify'])) {

			// >
			if (!current_user_can('read') && $registered_only) {
				die ('Please do not load this page directly. Thanks!');

			} else {
				if (isset($_POST['sac_no_js'])) {
					if (isset($_POST['sac_name']) && isset($_POST['sac_chat'])) {
						if ($_POST['sac_name'] != '' && $_POST['sac_chat'] != '') {
							if (isset($_POST['sac_url'])) {
								$sac_url = sanitize_text_field($_POST['sac_url']);
							} else {
								$sac_url = '';
							}
							$sac_name = sanitize_text_field($_POST['sac_name']);
							$sac_chat = sanitize_text_field($_POST['sac_chat']);
							$referrer = sanitize_text_field($_SERVER['HTTP_REFERER']);
							sac_addData($sac_name, $sac_chat, $sac_url);
							sac_deleteOld();

							setcookie("sacUserName", $sac_name, time() + 60 * 60 * 24 * 30 * 3, '/');
							setcookie("sacUrl", $sac_url, time() + 60 * 60 * 24 * 30 * 3, '/');
							header('location: ' . $referrer);
						} else {
							_e('Name and comment required.', 'sac');
						}
					}
				}
			}
			// >

		}
	}
}

// process chat data
function sac_addData($sac_user_name, $sac_user_text, $sac_user_url) {
	global $wpdb, $table_prefix, $sac_number_of_characters, $sac_username_length, $sac_options;
	
	$sac_user_text = substr(trim($sac_user_text), 0, $sac_number_of_characters);
	$sac_user_text = (empty($sac_user_text)) ? '' : sac_special_chars($sac_user_text);
	
	$sac_user_name = substr(trim($sac_user_name), 0, $sac_username_length);
	$sac_user_name = (empty($sac_user_name)) ? 'Anonymous' : sanitize_text_field($sac_user_name);
	
	$use_username  = $sac_options['sac_logged_name'];
	$current_user  = wp_get_current_user();
	$logged_name   = sanitize_text_field($current_user->display_name);
	
	if ($use_username && !empty($logged_name)) $sac_user_name = $logged_name;
	
	$sac_user_url  = ($sac_user_url == 'http://') ? '' : esc_url($sac_user_url);
	
	$query = $wpdb->get_row("SELECT * FROM $wpdb->options WHERE option_name = 'sac_censors'", ARRAY_A);
	$list  = $query['option_value'];

	$censors = explode(",", strval($list));
	$censors = array_map('trim', $censors);
	if (!empty($censors)) {
		foreach ($censors as $censor) {
			if (stristr($sac_user_text, $censor)) {
				$sac_user_text = str_ireplace($censor, '', $sac_user_text);
			}
			if (stristr($sac_user_name, $censor)) {
				$sac_user_name = str_ireplace($censor, '', $sac_user_name);
			}
			if (stristr($sac_user_url, $censor)) {
				$sac_user_url = str_ireplace($censor, '', $sac_user_url);
			}
		}
	}
	$ip = sac_get_ip_address();
	$wpdb->insert($table_prefix . "ajax_chat", array('time'=>time(), 'name'=>$sac_user_name, 'text'=>$sac_user_text, 'url'=>$sac_user_url, 'ip'=>$ip));
}

// clean up database
function sac_deleteOld() {
	global $wpdb, $table_prefix, $sac_number_of_comments;
	$a = intval($wpdb->insert_id);
	$b = intval($sac_number_of_comments);
	if (($a - $b) > $b) {
		$c = $a - $b;
		$wpdb->query($wpdb->prepare("DELETE FROM " . $table_prefix . "ajax_chat WHERE id < $c"));
	}
}

exit();


