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
}
