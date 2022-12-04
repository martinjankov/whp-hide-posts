<?php
/**
 * Plugin Name: WordPress Hide Posts
 * Description: Hides posts on home page, categories, search, tags page, authors page, RSS Feed as well as hiding Woocommerce products
 * Author:      MartinCV
 * Author URI:  https://www.martincv.com
 * Version:     1.1.1
 * Text Domain: whp-hide-posts
 *
 * WordPress Hide Posts is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * WordPress Hide Posts is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WordPress Hide Posts. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    WordPressHidePosts
 * @author     MartinCV
 * @since      0.0.1
 * @license    GPL-3.0+
 * @copyright  Copyright (c) 2022, MartinCV
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class
 */
final class WordPressHidePosts {
	/**
	 * Instance of the plugin
	 *
	 * @var WordPressHidePosts
	 */
	private static $instance;

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	private $version = '1.1.1';

	/**
	 * Instance of this plugin
	 *
	 * @return  WordPressHidePosts
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WordPressHidePosts ) ) {
			self::$instance = new WordPressHidePosts();
			self::$instance->constants();
			self::$instance->includes();

			add_action( 'plugins_loaded', array( self::$instance, 'run' ) );
		}

		return self::$instance;
	}

	/**
	 * 3rd party includes
	 *
	 * @return  void
	 */
	private function includes() {
		require_once WHP_PLUGIN_DIR . 'inc/core/autoloader.php';
		require_once WHP_PLUGIN_DIR . 'inc/core/helpers.php';
	}

	/**
	 * Define plugin constants
	 *
	 * @return  void
	 */
	private function constants() {
		// Plugin version.
		if ( ! defined( 'WHP_VERSION' ) ) {
			define( 'WHP_VERSION', $this->version );
		}

		// Plugin Folder Path.
		if ( ! defined( 'WHP_PLUGIN_DIR' ) ) {
			define( 'WHP_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'WHP_PLUGIN_URL' ) ) {
			define( 'WHP_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		}

		// Plugin Root File.
		if ( ! defined( 'WHP_PLUGIN_FILE' ) ) {
			define( 'WHP_PLUGIN_FILE', __FILE__ );
		}
	}

	/**
	 * Initialize classes / objects here
	 *
	 * @return  void
	 */
	public function run() {
		$this->load_textdomain();

		\MartinCV\WHP\Zeen_Theme::get_instance();

		// Init classes if is Admin/Dashboard.
		if ( is_admin() ) {
			\MartinCV\WHP\Admin\Dashboard::get_instance();
			\MartinCV\WHP\Admin\Post_Hide_Metabox::get_instance();
		} else {
			\MartinCV\WHP\Post_Hide::get_instance();
		}
	}

	/**
	 * Register textdomain
	 *
	 * @return  void
	 */
	private function load_textdomain() {
		load_plugin_textdomain( 'whp-hide-posts', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
}

WordPressHidePosts::instance();

if ( ! function_exists( 'whp_plugin' ) ) {
	/**
	 * Instance of the Plugin class which holds helper functions
	 *
	 * @return \MartinCV\WHP\Core\Plugin
	 */
	function whp_plugin() {
		return \MartinCV\WHP\Core\Plugin::get_instance();
	}
}
