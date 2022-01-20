<?php
/**
 * Helper functions
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
		$plugin = 'woocommerce/woocommerce.php';
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
	}
}

if ( ! function_exists( 'whp_admin_wc_product' ) ) {
	/**
	 * Check if current post is of type product.
	 *
	 * @return  bool
	 */
	function whp_admin_wc_product() {
		global $post;

		return 'product' === $post->post_type;
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
		$key = 'whp_' . $post_type . '_' . $from;

		$hidden_posts = wp_cache_get( $key, 'whp' );

		if ( $hidden_posts ) {
			return $hidden_posts;
		}

		$hidden_posts = get_transient( $key );

		if ( $hidden_posts ) {
			return $hidden_posts;
		}

		switch ( $from ) {
			case 'all':
					$meta_query = array(
						'relation' => 'OR',
						array(
							'key'     => '_whp_hide_on_frontpage',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => '_whp_hide_on_blog_page',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => '_whp_hide_on_categories',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => '_whp_hide_on_search',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => '_whp_hide_on_tags',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => '_whp_hide_on_authors',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => '_whp_hide_on_date',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => '_whp_hide_on_post_navigation',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => '_whp_hide_on_recent_posts',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => '_whp_hide_on_cpt_archive',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => '_whp_hide_on_cpt_tax',
							'compare' => 'EXISTS',
						),
					);
				break;
			case 'front_page':
				$meta_query = array(
					array(
						'key'     => '_whp_hide_on_frontpage',
						'compare' => 'EXISTS',
					),
				);
				break;
			case 'blog_page':
				$meta_query = array(
					array(
						'key'     => '_whp_hide_on_blog_page',
						'compare' => 'EXISTS',
					),
				);
				break;
			case 'categories':
				$meta_query = array(
					array(
						'key'     => '_whp_hide_on_categories',
						'compare' => 'EXISTS',
					),
				);
				break;
			case 'search':
				$meta_query = array(
					array(
						'key'     => '_whp_hide_on_search',
						'compare' => 'EXISTS',
					),
				);
				break;
			case 'tags':
				$meta_query = array(
					array(
						'key'     => '_whp_hide_on_tags',
						'compare' => 'EXISTS',
					),
				);
				break;
			case 'authors':
				$meta_query = array(
					array(
						'key'     => '_whp_hide_on_authors',
						'compare' => 'EXISTS',
					),
				);
				break;
			case 'date':
				$meta_query = array(
					array(
						'key'     => '_whp_hide_on_date',
						'compare' => 'EXISTS',
					),
				);
				break;
			case 'post_navigation':
				$meta_query = array(
					array(
						'key'     => '_whp_hide_on_post_navigation',
						'compare' => 'EXISTS',
					),
				);
				break;
			case 'recent_posts':
				$meta_query = array(
					array(
						'key'     => '_whp_hide_on_recent_posts',
						'compare' => 'EXISTS',
					),
				);
				break;
			case 'cpt_archive':
				$meta_query = array(
					array(
						'key'     => '_whp_hide_on_cpt_archive',
						'compare' => 'EXISTS',
					),
				);
				break;
			case 'cpt_tax':
				$meta_query = array(
					array(
						'key'     => '_whp_hide_on_cpt_tax',
						'compare' => 'EXISTS',
					),
				);
				break;
			default:
				return array();
		}

		$hidden_posts = new \WP_Query(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_query'     => $meta_query,
			)
		);

		$hidden_posts = $hidden_posts->posts;

		wp_cache_set( $key, $hidden_posts, 'whp' );

		set_transient( $key, $hidden_posts, 0 );

		return $hidden_posts;
	}
}

if ( ! function_exists( 'whp_get_enabled_post_types' ) ) {
	/**
	 * Fetch enabled posts types for this plugin
	 *
	 * @return  array
	 */
	function whp_get_enabled_post_types() {
		$key = 'whp_pt';

		$post_types = wp_cache_get( $key );

		if ( $post_types ) {
			return $post_types;
		}

		$post_types = array( 'post' );
		$enabled_post_types = get_option( 'whp_enabled_post_types', array() );

		if ( is_array( $enabled_post_types ) ) {
			$post_types = array_merge( $post_types, $enabled_post_types );
		} else {
			$post_type = array();
		}

		wp_cache_set( $key, $post_types, 'data' );

		return $post_types;
	}
}

if ( ! function_exists( 'whp_is_custom_post_type' ) ) {
	/**
	 * Check if post type for post is a CPT.
	 *
	 * @param   \WP_Post $post  Wordpress Post Object.
	 *
	 * @return  bool
	 */
	function whp_is_custom_post_type( $post = null ) {
		$all_custom_post_types = get_post_types( array( '_builtin' => false ) );

		// There are no custom post types.
		if ( empty( $all_custom_post_types ) ) {
			return false;
		}

		$custom_types      = array_keys( $all_custom_post_types );
		$current_post_type = get_post_type( $post );

		// Could not detect current type.
		if ( ! $current_post_type ) {
			return false;
		}

		return in_array( $current_post_type, $custom_types );
	}
}
