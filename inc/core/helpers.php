<?php
/**
 * Helper functions
 *
 * This file will be removed in coming verions and replaced by class-plugin.php
 *
 * @deprecated 1.0.0
 *
 * @package    WordPressHidePosts
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'whp_wc_exists' ) ) {
	/**
	 * Check if WooCOmmerce is active.
	 *
	 * @return  bool
	 */
	function whp_wc_exists() {
		return whp_plugin()->is_woocommerce_active();
	}
}

if ( ! function_exists( 'whp_admin_wc_product' ) ) {
	/**
	 * Check if current post is of type product.
	 *
	 * @return  bool
	 */
	function whp_admin_wc_product() {
		return whp_plugin()->is_woocommerce_product();
	}
}

if ( ! function_exists( 'whp_hidden_posts_ids' ) ) {
	/**
	 * Get all IDs for posts that are hidden
	 *
	 * @param   string $post_type  The post type to be filtered.
	 * @param   string $from Filter for the posts hidden on specific page.
	 *
	 * @return  array
	 */
	function whp_hidden_posts_ids( $post_type = 'post', $from = 'all' ) {
		return whp_plugin()->get_hidden_posts_ids( $post_type, $from );
	}
}

if ( ! function_exists( 'whp_get_enabled_post_types' ) ) {
	/**
	 * Fetch enabled posts types for this plugin
	 *
	 * @return  array
	 */
	function whp_get_enabled_post_types() {
		return whp_plugin()->get_enabled_post_types();
	}
}

if ( ! function_exists( 'whp_is_custom_post_type' ) ) {
	/**
	 * Check if post type for post is a CPT.
	 *
	 * @param   \WP_Post $post  WordPress Post Object.
	 *
	 * @return  bool
	 */
	function whp_is_custom_post_type( $post = null ) {
		return whp_plugin()->is_custom_post_type( $post );
	}
}
