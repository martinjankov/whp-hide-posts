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
 * Constants
 */
class Constants {
	const HIDDEN_POSTS_KEYS_LIST = array(
		'all'             => '_whp_hide_%',
		'front_page'      => '_whp_hide_on_frontpage',
		'blog_page'       => '_whp_hide_on_blog_page',
		'categories'      => '_whp_hide_on_categories',
		'search'          => '_whp_hide_on_search',
		'tags'            => '_whp_hide_on_tags',
		'authors'         => '_whp_hide_on_authors',
		'date'            => '_whp_hide_on_date',
		'post_navigation' => '_whp_hide_on_post_navigation',
		'recent_posts'    => '_whp_hide_on_recent_posts',
		'cpt_archive'     => '_whp_hide_on_cpt_archive',
		'rest_api'        => '_whp_hide_on_rest_api',
	);

	const BUILT_IN_TAXONOMIES = array(
		'category',
		'post_tag',
		'post_format',
	);

	/**
	 * Private constructor
	 *
	 * @return  void
	 */
	private function __construct() {
		// Empty here.
	}
}
