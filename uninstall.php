<?php // uninstall remove options

if(!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) exit();

// delete options
delete_option('sac_options');
delete_option('sac_censors');

// delete transients
// delete_transient('sac_transient');

// delete custom tables
global $wpdb;
$table_name = $wpdb->prefix . 'ajax_chat';
$wpdb->query("DROP TABLE IF EXISTS {$table_name}");




