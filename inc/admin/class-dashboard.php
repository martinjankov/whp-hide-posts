<?php
/**
 * Admin dashboard settings
 *
 * @package    WordPressHidePosts
 */

namespace MartinCV\WHP\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin dashboard class
 */
class Dashboard {
	use \MartinCV\WHP\Traits\Singleton;

	/**
	 * Initialize class
	 *
	 * @return  void
	 */
	private function initialize() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}

	/**
	 * Register new submenu and Settings
	 *
	 * @return  void
	 */
	public function menu() {
		add_submenu_page(
			'options-general.php',
			__( 'Hide Posts', 'whp-hide-posts' ),
			__( 'Hide Posts', 'whp-hide-posts' ),
			'administrator',
			'whp-settings',
			array( $this, 'settings' )
		);
	}

	/**
	 * Show the settings form with options
	 *
	 * @return  void
	 */
	public function settings() {
		$post_types                   = get_post_types( array( 'public' => true ), 'object' );
		$enabled_post_types           = whp_plugin()->get_enabled_post_types();
		$whp_disable_hidden_on_column = get_option( 'whp_disable_hidden_on_column', false );

		require_once WHP_PLUGIN_DIR . 'views/admin/template-admin-dashboard.php';
	}

	/**
	 * Register plugin settings
	 *
	 * @return  void
	 */
	public function register_settings() {
		register_setting( 'whp-settings-group', 'whp_enabled_post_types' );
		register_setting( 'whp-settings-group', 'whp_disable_hidden_on_column' );
	}

	public function migrate_meta_to_table() {
		global $wpdb;

		$meta_keys = [
			'_whp_hide_on_frontpage'    => 'hide_on_frontpage',
			'_whp_hide_on_blog_page'    => 'hide_on_blog_page',
			'_whp_hide_on_cpt_archive'  => 'hide_on_cpt_archive',
			'_whp_hide_on_categories'   => 'hide_on_categories',
			'_whp_hide_on_search'       => 'hide_on_search',
			'_whp_hide_on_tags'         => 'hide_on_tags',
			'_whp_hide_on_authors'      => 'hide_on_authors',
			'_whp_hide_on_date'         => 'hide_on_date',
			'_whp_hide_in_rss_feed'     => 'hide_in_rss_feed',
			'_whp_hide_on_store'        => 'hide_on_store',
			'_whp_hide_on_product_category' => 'hide_on_product_category',
			'_whp_hide_on_single_post_page' => 'hide_on_single_post_page',
		];

		foreach ( $meta_keys as $meta_key => $condition ) {
			$posts = $wpdb->get_results(
				$wpdb->prepare(
					"
					SELECT post_id
					FROM {$wpdb->postmeta}
					WHERE meta_key = %s
					",
					$meta_key
				)
			);

			$table_name = $wpdb->prefix . 'whp_posts_visibility';

			foreach ( $posts as $post ) {
				$exist = whp_plugin()->get_whp_meta( $post->post_id, $condition );

				if ( $exist ) {
					continue;
				}

				$wpdb->insert(
					$table_name,
					array(
						'post_id'   => $post->post_id,
						'condition' => $condition,
					),
					array(
						'%d',
						'%s',
					)
				);

				delete_post_meta( $post->post_id, $meta_key );
			}
		}
	}
}
