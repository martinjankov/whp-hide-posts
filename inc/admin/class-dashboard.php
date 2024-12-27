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
		add_action( 'admin_notices', array( $this, 'migrate_data_notice' ) );
		add_action( 'admin_init', array( $this, 'handle_migration_action' ) );
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

	/**
	 * Migrate hide posts data from meta to table
	 *
	 * @return void
	 */
	public function migrate_meta_to_table() {
		$data_migrated = get_option( 'whp_data_migrated', false );

		if ( $data_migrated ) {
			return;
		}

		global $wpdb;

		$table_name = $wpdb->prefix . 'whp_posts_visibility';

		$table_exists = $wpdb->get_var(
			$wpdb->prepare(
				"SHOW TABLES LIKE %s",
				$table_name
			)
		);

		if ( $table_exists !== $table_name ) {
			return;
		}

		$meta_keys = [
			'_whp_hide_on_frontpage'        => 'hide_on_frontpage',
			'_whp_hide_on_blog_page'        => 'hide_on_blog_page',
			'_whp_hide_on_cpt_archive'      => 'hide_on_cpt_archive',
			'_whp_hide_on_categories'       => 'hide_on_categories',
			'_whp_hide_on_search'           => 'hide_on_search',
			'_whp_hide_on_tags'             => 'hide_on_tags',
			'_whp_hide_on_authors'          => 'hide_on_authors',
			'_whp_hide_on_date'             => 'hide_on_date',
			'_whp_hide_in_rss_feed'         => 'hide_in_rss_feed',
			'_whp_hide_on_store'            => 'hide_on_store',
			'_whp_hide_on_product_category' => 'hide_on_product_category',
			'_whp_hide_on_single_post_page' => 'hide_on_single_post_page',
			'_whp_hide_on_post_navigation'  => 'hide_on_post_navigation',
			'_whp_hide_on_recent_posts'     => 'hide_on_recent_posts',
			'_whp_hide_on_archive'          => 'hide_on_archive',
			'_whp_hide_on_rest_api'         => 'hide_on_rest_api',
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

		update_option( 'whp_data_migrated', true );
	}

	/**
	 * Notice to migrate data
	 *
	 * @return void
	 */
	public function migrate_data_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$data_migrated_notice_closed = get_option( 'whp_data_migrated_notice_closed', false );

		if ( $data_migrated_notice_closed ) {
			return;
		}

		$data_migrated = get_option( 'whp_data_migrated', false );

		if ( $data_migrated ) {
			$action_url = add_query_arg(
				array(
					'action' => 'whp_hide_posts_migration_complete_notice_close',
					'__nonce' => wp_create_nonce( 'whp-hide-posts-migration-complete-nonce' ),
				),
				admin_url()
			);

			echo '<div class="notice notice-success">';
			echo '<p>Migaration Complete.</p>';
			echo '<p><a href="' . esc_url( $action_url ) . '" class="button button-primary">Close Notice</a></p>';
			echo '</div>';
			return;
		}

		$action_url = add_query_arg(
			array(
				'action' => 'whp_hide_posts_migrate_data',
				'__nonce' => wp_create_nonce( 'whp-hide-posts-migrate-data-nonce' ),
			),
			admin_url()
		);

		echo '<div class="notice notice-warning is-dismissible">';
		echo '<p>Important: We implemented new table for managing the hide flags in our plugin which optimizes the query and improve overall performance. <strong>Please create database backup before proceeding, just in case.</strong></p>';
		echo '<p><a href="' . esc_url( $action_url ) . '" class="button button-primary">Migrate Hide Post Data</a></p>';
		echo '</div>';
	}

	/**
	 * Handle the migration action
	 *
	 * @return void
	 */
	public function handle_migration_action() {
		$data_migrated = get_option( 'whp_data_migrated', false );

		if ( $data_migrated ) {
			$data_migrated_notice_closed = get_option( 'whp_data_migrated_notice_closed', false );

			if ( ! $data_migrated_notice_closed ) {
				if ( ! isset( $_GET['action'] ) || 'whp_hide_posts_migration_complete_notice_close' !== $_GET['action'] ) {
					return;
				}

				if ( ! isset( $_GET['__nonce'] ) || ! wp_verify_nonce( $_GET['__nonce'], 'whp-hide-posts-migration-complete-nonce' ) ) {
					return;
				}

				update_option( 'whp_data_migrated_notice_closed', true );

				wp_safe_redirect( remove_query_arg( array( 'action', '__nonce' ) ) );
				exit;
			}

			return;
		}

		if ( ! isset( $_GET['action'] ) || 'whp_hide_posts_migrate_data' !== $_GET['action'] ) {
			return;
		}

		if ( ! isset( $_GET['__nonce'] ) || ! wp_verify_nonce( $_GET['__nonce'], 'whp-hide-posts-migrate-data-nonce' ) ) {
			return;
		}

		$this->migrate_meta_to_table();

		wp_safe_redirect( remove_query_arg( array( 'action', '__nonce' ) ) );
        exit;
	}
}
