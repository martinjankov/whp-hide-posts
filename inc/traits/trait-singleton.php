<?php
/**
 * Signleton trait
 *
 * @package    WordPressHidePosts
 */

namespace MartinCV\WHP\Traits;

trait Singleton {
	/**
	 * Instance of the object
	 *
	 * @var \Object
	 */
	private static $instance = null;

	/**
	 * Setup singleton instance
	 *
	 * @return  \Object
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Private consturct
	 *
	 * @return  void
	 */
	private function __construct() {
		if ( method_exists( $this, 'initialize' ) ) {
			$this->initialize();
		}
	}
}
