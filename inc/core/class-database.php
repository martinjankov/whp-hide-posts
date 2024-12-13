<?php
/**
 * Database handling
 *
 * @package ALAStoresInventory
 */

namespace MartinCV\WHP\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Database
 */
class Database {
	use \MartinCV\WHP\Traits\Singleton;

	/**
	 * Create database tables
	 *
	 * @return  void
	 */
	public function create_tables() {
		$current_db_version = 1;
		$db_version         = get_option( 'whp_db_version', 0 );

		if ( $current_db_version === (int) $db_version ) {
			return;
		}

		global $wpdb;

		$whp_posts_visibility_table = $wpdb->prefix . 'whp_posts_visibility';

		$charset_collate = $wpdb->get_charset_collate();

		$whp_posts_visibility = "CREATE TABLE $whp_posts_visibility_table (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			post_id BIGINT(20) UNSIGNED NOT NULL,
			`condition` VARCHAR(100) NOT NULL,
			PRIMARY KEY (id),
			INDEX pid_con (post_id,`condition`)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $whp_posts_visibility );

		update_option( 'whp_db_version', $current_db_version );
	}
}
