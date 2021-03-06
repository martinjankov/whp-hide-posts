<?php
/**
 * Plugin Name: Wordpress Hide Posts
 * Description: Hides posts on home page, categories, search, tags page, authors page, RSS Feed as well as hiding Woocommere products
 * Author:      MartinCV
 * Author URI:  https://www.martincv.com
 * Version:     1.0.0
 * Text Domain: whp
 *
 * Wordpress Hide Posts is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Wordpress Hide Posts is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Custom Page Number Per Page. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    wordpress-hide-posts
 * @author     MartinCV
 * @since      0.0.1
 * @license    GPL-3.0+
 * @copyright  Copyright (c) 2021, MartinCV
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class
 */
final class WordpressHidePosts {
    /**
     * Instance of the plugin
     *
     * @var WordpressHidePosts
     */
	private static $_instance;

    /**
     * Plugin version
     *
     * @var string
     */
	private $_version = '1.0.0';

	public static function instance() {
		if ( ! isset( self::$_instance ) && ! ( self::$_instance instanceof WordpressHidePosts ) ) {
			self::$_instance = new WordpressHidePosts;
            self::$_instance->constants();
			self::$_instance->includes();

            add_action( 'plugins_loaded', [ self::$_instance, 'objects' ] );
            add_action( 'plugins_loaded', [ self::$_instance, 'load_textdomain' ] );
        }

		return self::$_instance;
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
		// Plugin version
		if ( ! defined( 'WHP_VERSION' ) ) {
			define( 'WHP_VERSION', $this->_version );
		}

		// Plugin Folder Path
		if ( ! defined( 'WHP_PLUGIN_DIR' ) ) {
			define( 'WHP_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'WHP_PLUGIN_URL' ) ) {
			define( 'WHP_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		}

		// Plugin Root File
		if ( ! defined( 'WHP_PLUGIN_FILE' ) ) {
			define( 'WHP_PLUGIN_FILE', __FILE__ );
		}
	}

    /**
     * Initialize classes / objects here
     *
     * @return  void
     */
	public function objects() {
		// Global objects.
        \MartinCV\WHP\Post_Hide::get_instance();

		// Init classes if is Admin/Dashboard.
		if ( is_admin() ) {
			\MartinCV\WHP\Admin\Admin_Dashboard::get_instance();
			\MartinCV\WHP\Admin\Post_Hide_Metabox::get_instance();
		}
	}

    /**
     * Register textdomain
     *
     * @return  void
     */
    public function load_textdomain() {
		load_plugin_textdomain( 'whp', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
}

WordpressHidePosts::instance();
