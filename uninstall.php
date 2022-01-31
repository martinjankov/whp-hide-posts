<?php
/**
 * Run on pluigin uninstall
 *
 * @package    WordPressHidePosts
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'whp_enabled_post_types' );

global $wpdb;
$query = "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE '_whp_hide_on_%'";
$wpdb->query( $query );
