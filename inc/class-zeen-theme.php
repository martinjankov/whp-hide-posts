<?php
/**
 * Zeen theme support
 *
 * @link https://themeforest.net/item/zeen-next-generation-magazine-wordpress-theme/22709856
 *
 * @package    WordPressHidePosts
 */

namespace MartinCV\WHP;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zeen Theme class.
 */
class Zeen_Theme {
	use \MartinCV\WHP\Traits\Singleton;

	/**
	 * Initialize class
	 *
	 * @return  void
	 */
	private function initialize() {
		add_filter( 'zeen_pagination_query', array( $this, 'zeen_pagination_query' ) );
	}

	/**
	 * Load more zeen query compabitlity
	 *
	 * @param  array $qry The query.
	 *
	 * @return array
	 */
	public function zeen_pagination_query( $qry = array() ) {
		if ( ! is_array( $qry ) ) {
			return $qry;
		}

		$post_type = ! empty( $qry['post_type'] ) ? $qry['post_type'] : 'post';

		$plugin_post_ids_hidden = whp_plugin()->get_hidden_posts_ids( $post_type );

		$qry['post__not_in'] = empty( $qry['post__not_in'] ) ? $plugin_post_ids_hidden : array_unique( array_merge( $plugin_post_ids_hidden, $qry['post__not_in'] ) );

		return $qry;
	}
}
