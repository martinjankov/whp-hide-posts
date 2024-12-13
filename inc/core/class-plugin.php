<?php
/**
 * Plugin class
 *
 * @since 1.0.0
 *
 * @package    WordPressHidePosts
 */

namespace MartinCV\WHP\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin class
 */
class Plugin {
	use \MartinCV\WHP\Traits\Singleton;

	/**
	 * Check if WooCommerce is active.
	 *
	 * @return  bool
	 */
	public function is_woocommerce_active() {
		$plugin = 'woocommerce/woocommerce.php';

		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true );
	}

	/**
	 * Check if current post is of type product.
	 *
	 * @return  bool
	 */
	public function is_woocommerce_product() {
		global $post;

		return 'product' === $post->post_type;
	}

	/**
	 * Get all IDs for posts that are hidden
	 *
	 * @param   string $post_type  The post type to be filtered.
	 * @param   string $from Filter for the posts hidden on specific page.
	 * @param   boolean $fallback Should it fallback to meta table.
	 *
	 * @return  array
	 */
	public function get_hidden_posts_ids( $post_type = 'post', $from = 'all', $fallback = false ) {
		$cache_key = 'whp_' . $post_type . '_' . $from;

		$hidden_posts = wp_cache_get( $cache_key, 'whp' );

		if ( $hidden_posts ) {
			return $hidden_posts;
		}

		$hidden_posts = get_transient( $cache_key );

		if ( $hidden_posts ) {
			return $hidden_posts;
		}

		$key = Constants::HIDDEN_POSTS_KEYS_LIST[ $from ] ?? false;

		if ( ! $key ) {
			return array();
		}

		$key = str_replace( '_whp_', '', $key );

		global $wpdb;

		$table_name = $wpdb->prefix . 'whp_posts_visibility';

		$sql = $wpdb->prepare( "SELECT DISTINCT post_id FROM {$table_name} WHERE `condition` = %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)", $key, $post_type );

		if ( 'all' === $key ) {
			$sql = $wpdb->prepare( "SELECT DISTINCT post_id FROM {$table_name} WHERE `condition` LIKE %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)", $key, $post_type );
		}

		$hidden_posts = $wpdb->get_col( $sql );

		if ( empty( $hidden_posts ) && $fallback ) {
			$key = '_whp_' . $key;

			$sql = $wpdb->prepare( "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)", $key, $post_type );

			if ( 'all' === $key ) {
				$sql = $wpdb->prepare( "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)", $key, $post_type );
			}

			$hidden_posts = $wpdb->get_col( $sql );
		}

		wp_cache_set( $cache_key, $hidden_posts, 'whp' );

		set_transient( $cache_key, $hidden_posts, WEEK_IN_SECONDS );

		return $hidden_posts;
	}

	/**
	 * Fetch enabled posts types for this plugin
	 *
	 * @return  array
	 */
	public function get_enabled_post_types() {
		$key = 'whp_pt';

		$post_types = wp_cache_get( $key, 'whp' );

		if ( $post_types ) {
			return $post_types;
		}

		$post_types         = array( 'post' );
		$enabled_post_types = get_option( 'whp_enabled_post_types', array() );

		if ( is_array( $enabled_post_types ) ) {
			$post_types = array_merge( $post_types, $enabled_post_types );
		}

		wp_cache_set( $key, $post_types, 'whp' );

		return $post_types;
	}

	/**
	 * Check if post type for post is a CPT.
	 *
	 * @param   \WP_Post $post  WordPress Post Object.
	 *
	 * @return  bool
	 */
	public function is_custom_post_type( $post = null ) {
		$all_custom_post_types = get_post_types( array( '_builtin' => false ) );

		if ( empty( $all_custom_post_types ) ) {
			return false;
		}

		$custom_types      = array_keys( $all_custom_post_types );
		$current_post_type = get_post_type( $post );

		if ( ! $current_post_type ) {
			return false;
		}

		return in_array( $current_post_type, $custom_types, true );
	}

	/**
	 * Check if post is hidden in the custom table
	 *
	 * @param  int  $post_id  The post id.
	 * @param  string  $key      The key name.
	 * @param  boolean $fallback Should it check in the post meta table.
	 *
	 * @return boolean
	 */
	public function get_whp_meta( $post_id, $key, $fallback = false ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'whp_posts_visibility';

		$hidden_post = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table_name} WHERE post_id = %d AND `condition` = %s",
				$post_id,
				$key
			)
		);

		if ( $hidden_post ) {
			return true;
		}

		if ( $fallback ) {
			return get_post_meta( $post_id, '_whp_' . $key, true );
		}
	}

	/**
	 * Set post for hiding
	 *
	 * @param  int    $post_id  The post id.
	 * @param  string $key      The key name.
	 *
	 * @return boolean
	 */
	public function add_whp_meta( $post_id, $key, $fallback = false ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'whp_posts_visibility';

		$wpdb->insert(
			$table_name,
			array(
				'post_id' => $post_id,
				'condition' => $key,
			),
			array(
				'%d',
				'%s',
			)
		);
	}

	/**
	 * Remove post from hiding
	 *
	 * @param  int    $post_id  The post id.
	 * @param  string $key      The key name.
	 *
	 * @return boolean
	 */
	public function delete_whp_meta( $post_id, $key, $fallback = false ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'whp_posts_visibility';

		$wpdb->delete(
			$table_name,
			array(
				'post_id' => $post_id,
				'condition' => $key,
			),
			array(
				'%d',
				'%s',
			)
		);

		if ( $fallback ) {
			delete_post_meta( $post_id, '_whp_' . $key );
		}
	}
}
