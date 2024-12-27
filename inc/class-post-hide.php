<?php
/**
 * Logic for hiding posts happens here.
 *
 * @package    WordPressHidePosts
 */

namespace MartinCV\WHP;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post_Hide class.
 */
class Post_Hide {
	use \MartinCV\WHP\Traits\Singleton;

	/**
	 * Enabled post types
	 *
	 * @var array
	 */
	private $enabled_post_types = array();

	/**
	 * Initialize class
	 *
	 * @return  void
	 */
	private function initialize() {
		$this->enabled_post_types = whp_plugin()->get_enabled_post_types();

		add_action( 'pre_get_posts', array( $this, 'exclude_posts' ), 99 );
		add_action( 'parse_query', array( $this, 'parse_query' ) );
		add_filter( 'get_next_post_where', array( $this, 'hide_from_post_navigation' ), 10, 1 );
		add_filter( 'get_previous_post_where', array( $this, 'hide_from_post_navigation' ), 10, 1 );
		add_filter( 'widget_posts_args', array( $this, 'hide_from_recent_post_widget' ), 10, 1 );

		foreach ( $this->enabled_post_types as $pt ) {
			if ( 'product' !== $pt ) {
				add_filter( "rest_{$pt}_query", array( $this, 'hide_from_rest_api' ), 10, 2 );
			} else {
				add_filter( 'woocommerce_rest_product_object_query', array( $this, 'hide_from_rest_api' ), 10, 2 );
				add_filter( 'woocommerce_rest_product_query', array( $this, 'hide_from_rest_api' ), 10, 2 );
			}
		}
	}

	/**
	 * Hide from rest api
	 *
	 * @param  array           $args    The query args.
	 * @param  WP_REST_Request $request The request.
	 *
	 * @return array
	 */
	public function hide_from_rest_api( $args, $request ) {
		if ( ! in_array( $args['post_type'], $this->enabled_post_types, true ) ) {
			return $args;
		}

		$hidden_ids = whp_plugin()->get_hidden_posts_ids( $args['post_type'], 'rest_api' );

		if ( ! empty( $hidden_ids ) ) {
			$args['post__not_in'] = ! empty( $args['post__not_in'] ) ? array_unique( array_merge( $hidden_ids, $args['post__not_in'] ) ) : $hidden_ids;
		}

		return $args;
	}

	/**
	 * A workaround for the is_front_page() check inside pre_get_posts and later hooks.
	 *
	 * Based on the patch from @mattonomics in #27015
	 *
	 * @param \WP_Query $query The WordPress query object.
	 *
	 * @see http://wordpress.stackexchange.com/a/188320/26350
	 */
	public function parse_query( $query ) {
		if ( is_null( $query->queried_object ) && $query->get( 'page_id' ) ) {
			$query->queried_object    = get_post( $query->get( 'page_id' ) );
			$query->queried_object_id = (int) $query->get( 'page_id' );
		}
	}

	/**
	 * Exclude posts with enabled hide options
	 *
	 * @param  WP_Query $query Current query object.
	 *
	 * @return void
	 */
	public function exclude_posts( $query ) {
		global $wpdb;

		$q_post_type = $query->get( 'post_type' );

		if ( ( ! is_admin() || ( is_admin() && wp_doing_ajax() ) ) &&
			(
				empty( $query->get( 'post_type' ) ) ||
				( ! is_array( $q_post_type ) && in_array( $q_post_type, $this->enabled_post_types, true ) ) ||
				( is_array( $q_post_type ) && ! empty( array_intersect( $q_post_type, $this->enabled_post_types ) ) )
			)
		) {
			$table_name = $wpdb->prefix . 'whp_posts_visibility';
			// Handle single post pages
			if ( is_singular( $q_post_type ) && ! $query->is_main_query() ) {
				$hidden_posts = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT DISTINCT post_id FROM {$table_name} WHERE `condition` = %s",
						'hide_on_single_post_page'
					)
				);

				if ( ! empty( $hidden_posts ) ) {
					$query->set( 'post__not_in', $hidden_posts );
				} else {
					// Fallback to meta
					$query->set( 'meta_key', '_whp_hide_on_single_post_page' );
					$query->set( 'meta_compare', 'NOT EXISTS' );
				}
			} elseif ( ( is_front_page() && is_home() ) || is_front_page() ) {
				$this->exclude_by_condition( $query, 'hide_on_frontpage', '_whp_hide_on_frontpage' );
			} elseif ( is_home() ) {
				$this->exclude_by_condition( $query, 'hide_on_blog_page', '_whp_hide_on_blog_page' );
			} elseif ( is_post_type_archive( $q_post_type ) ) {
				$this->exclude_by_condition( $query, 'hide_on_cpt_archive', '_whp_hide_on_cpt_archive' );
			} elseif ( is_category( $q_post_type ) ) {
				$this->exclude_by_condition( $query, 'hide_on_categories', '_whp_hide_on_categories' );
			} elseif ( is_tag() ) {
				$this->exclude_by_condition( $query, 'hide_on_tags', '_whp_hide_on_tags' );
			} elseif ( is_author() ) {
				$this->exclude_by_condition( $query, 'hide_on_authors', '_whp_hide_on_authors' );
			} elseif ( is_date() ) {
				$this->exclude_by_condition( $query, 'hide_on_date', '_whp_hide_on_date' );
			} elseif ( is_search() ) {
				$this->exclude_by_condition( $query, 'hide_on_search', '_whp_hide_on_search' );
			} elseif ( is_feed() ) {
				$this->exclude_by_condition( $query, 'hide_in_rss_feed', '_whp_hide_in_rss_feed' );
			} elseif ( whp_plugin()->is_woocommerce_active() ) {
				if ( is_shop() ) {
					$this->exclude_by_condition( $query, 'hide_on_store', '_whp_hide_on_store' );
				} elseif ( is_product_category() ) {
					$this->exclude_by_condition( $query, 'hide_on_product_category', '_whp_hide_on_product_category' );
				}
			} elseif ( is_archive() ) {
				$this->exclude_by_condition( $query, 'hide_on_archive', '_whp_hide_on_archive' );
			}
		}
	}

	/**
	 * Exclude posts by a condition using the custom table, with a fallback to post meta.
	 *
	 * @param WP_Query $query The query object.
	 * @param string   $condition The condition to check in the custom table.
	 * @param string   $meta_key The meta key to check for fallback.
	 */
	private function exclude_by_condition( &$query, $condition, $meta_key ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'whp_posts_visibility';

		$hidden_posts = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT post_id FROM {$table_name} WHERE `condition` = %s",
				$condition
			)
		);

		if ( ! empty( $hidden_posts ) ) {
			$query->set( 'post__not_in', $hidden_posts );
		} else {
			$data_migrated = get_option( 'whp_data_migrated', false );

			if ( ! $data_migrated ) {
				// Fallback to meta
				$query->set( 'meta_key', $meta_key );
				$query->set( 'meta_compare', 'NOT EXISTS' );
			}
		}
	}

	/**
	 * Hide post from post navigation
	 *
	 * @param   string $where The WHERE part of the query.
	 *
	 * @return  string
	 */
	public function hide_from_post_navigation( $where ) {
		$data_migrated = get_option( 'whp_data_migrated', false );

		$fallback = ! $data_migrated;

		$hidden_on_post_navigation = whp_plugin()->get_hidden_posts_ids( 'post', 'post_navigation', $fallback );

		if ( empty( $hidden_on_post_navigation ) ) {
			return $where;
		}

		$ids_placeholders = array_fill( 0, count( $hidden_on_post_navigation ), '%d' );
		$ids_placeholders = implode( ', ', $ids_placeholders );

		global $wpdb;

		$where .= $wpdb->prepare( " AND ID NOT IN ( $ids_placeholders )", ...$hidden_on_post_navigation );

		return $where;
	}

	/**
	 * Hide posts from default WordPress recent post widget
	 *
	 * @param   array $query_args WP_Query arguments.
	 *
	 * @return  array
	 */
	public function hide_from_recent_post_widget( $query_args ) {
		$data_migrated = get_option( 'whp_data_migrated', false );

		$fallback = ! $data_migrated;

		$hidden_on_recent_posts = whp_plugin()->get_hidden_posts_ids( 'post', 'recent_posts', $fallback );

		if ( empty( $hidden_on_recent_posts ) ) {
			return $query_args;
		}

		$query_args['post__not_in'] = $hidden_on_recent_posts;

		return $query_args;
	}
}
