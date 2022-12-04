<?php
/**
 * Hide Posts Metabox class
 *
 * @package    WordPressHidePosts
 */

namespace MartinCV\WHP\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post_Hide_Metabox class.
 */
class Post_Hide_Metabox {
	use \MartinCV\WHP\Traits\Singleton;

	/**
	 * Initialize class
	 *
	 * @return  void
	 */
	private function initialize() {
		$disable_hidden_on_column = get_option( 'whp_disable_hidden_on_column' );

		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'save_post_metabox' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_assets' ) );

		if ( ! $disable_hidden_on_column ) {
			$enabled_post_types = whp_plugin()->get_enabled_post_types();

			foreach ( $enabled_post_types as $pt ) {
				add_action( 'manage_' . $pt . '_posts_custom_column', array( $this, 'render_post_columns' ), 10, 2 );
				add_filter( 'manage_' . $pt . '_posts_columns', array( $this, 'add_post_columns' ) );
			}
		}
	}

	/**
	 * Load admin assets
	 *
	 * @return  void
	 */
	public function load_admin_assets() {
		global $post;

		if ( ! $post ) {
			return;
		}

		$enabled_post_types = whp_plugin()->get_enabled_post_types();

		if ( ! in_array( $post->post_type, $enabled_post_types, true ) ) {
			return;
		}

		wp_enqueue_script(
			'whp-admin-post-script',
			WHP_PLUGIN_URL . 'assets/admin/js/whp-script.js',
			array( 'jquery' ),
			WHP_VERSION,
			true
		);

		wp_localize_script(
			'whp-admin-post-script',
			'whpPlugin',
			array(
				'selectTaxonomyLabel' => __( 'Select Taxonomy', 'whp-hide-posts' ),
			)
		);

		wp_enqueue_style(
			'whp-admin-post-style',
			WHP_PLUGIN_URL . 'assets/admin/css/whp-style.css',
			array(),
			WHP_VERSION
		);
	}

	/**
	 * Add Post Hide metabox in sidebar top
	 *
	 * @return void
	 */
	public function add_metabox() {
		$post_types = whp_plugin()->get_enabled_post_types();

		add_meta_box(
			'hide_posts',
			__( 'Hide Posts', 'whp-hide-posts' ),
			array( $this, 'metabox_callback' ),
			$post_types,
			'side',
			'high'
		);
	}

	/**
	 * Add custom columns in posts list table
	 *
	 * @param   array $columns Columns shown on the posts list.
	 *
	 * @return  array
	 */
	public function add_post_columns( $columns ) {
		global $post;

		$columns['hidden_on'] = __( 'Hidden On', 'whp-hide-posts' );

		return $columns;
	}

	/**
	 * Show on which pages the post is hidden
	 *
	 * @param   string $column_name The column name shown in the posts list table.
	 * @param   int    $post_id The current post id.
	 *
	 * @return  void
	 */
	public function render_post_columns( $column_name, $post_id ) {
		if ( 'hidden_on' !== $column_name ) {
			return;
		}

		$whp_hide_on_frontpage        = get_post_meta( $post_id, '_whp_hide_on_frontpage', true );
		$whp_hide_on_categories       = get_post_meta( $post_id, '_whp_hide_on_categories', true );
		$whp_hide_on_search           = get_post_meta( $post_id, '_whp_hide_on_search', true );
		$whp_hide_on_tags             = get_post_meta( $post_id, '_whp_hide_on_tags', true );
		$whp_hide_on_authors          = get_post_meta( $post_id, '_whp_hide_on_authors', true );
		$whp_hide_in_rss_feed         = get_post_meta( $post_id, '_whp_hide_in_rss_feed', true );
		$whp_hide_on_blog_page        = get_post_meta( $post_id, '_whp_hide_on_blog_page', true );
		$whp_hide_on_date             = get_post_meta( $post_id, '_whp_hide_on_date', true );
		$whp_hide_on_post_navigation  = get_post_meta( $post_id, '_whp_hide_on_post_navigation', true );
		$whp_hide_on_recent_posts     = get_post_meta( $post_id, '_whp_hide_on_recent_posts', true );
		$whp_hide_on_cpt_archive      = get_post_meta( $post_id, '_whp_hide_on_cpt_archive', true );
		$whp_hide_on_archive          = get_post_meta( $post_id, '_whp_hide_on_archive', true );
		$whp_hide_on_rest_api         = get_post_meta( $post_id, '_whp_hide_on_rest_api', true );
		$whp_hide_on_single_post_page = get_post_meta( $post_id, '_whp_hide_on_single_post_page', true );

		if ( whp_plugin()->is_woocommerce_active() && whp_plugin()->is_woocommerce_product() ) {
			$whp_hide_on_store            = get_post_meta( $post_id, '_whp_hide_on_store', true );
			$whp_hide_on_product_category = get_post_meta( $post_id, '_whp_hide_on_product_category', true );
		}

		$whp_hide_on = '';

		if ( $whp_hide_on_frontpage ) {
			$whp_hide_on .= __( 'Front / Home page', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_archive ) {
			$whp_hide_on .= __( 'Archives', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_categories ) {
			$whp_hide_on .= __( 'Categories archive', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_search ) {
			$whp_hide_on .= __( 'Search page', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_tags ) {
			$whp_hide_on .= __( 'Tags archive', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_authors ) {
			$whp_hide_on .= __( 'Authors archive', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_in_rss_feed ) {
			$whp_hide_on .= __( 'RSS feed', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_blog_page ) {
			$whp_hide_on .= __( 'Blog page', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_date ) {
			$whp_hide_on .= __( 'Date archive', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_post_navigation ) {
			$whp_hide_on .= __( 'Single post navigation', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_recent_posts ) {
			$whp_hide_on .= __( 'Recent Posts Widget', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_cpt_archive ) {
			$whp_hide_on .= __( 'CPT Archive page', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_rest_api ) {
			$whp_hide_on .= __( 'REST API', 'whp-hide-posts' ) . ', ';
		}

		if ( isset( $whp_hide_on_store ) && $whp_hide_on_store ) {
			$whp_hide_on .= __( 'Store page', 'whp-hide-posts' ) . ', ';
		}

		if ( isset( $whp_hide_on_product_category ) && $whp_hide_on_product_category ) {
			$whp_hide_on .= __( 'Product category page', 'whp-hide-posts' ) . ', ';
		}

		if ( $whp_hide_on_single_post_page ) {
			$whp_hide_on .= __( 'Single Post Page', 'whp-hide-posts' ) . ', ';
		}

		if ( '' !== $whp_hide_on ) {
			$whp_hide_on = rtrim( $whp_hide_on, ', ' );

			echo esc_html( $whp_hide_on );
		}
	}

	/**
	 * Show the metabox template in sidebar top
	 *
	 * @param  WP_Post $post Current post object.
	 *
	 * @return void
	 */
	public function metabox_callback( $post ) {
		wp_nonce_field( 'wp_metabox_nonce', 'wp_metabox_nonce_value' );

		$whp_hide_on_frontpage        = get_post_meta( $post->ID, '_whp_hide_on_frontpage', true );
		$whp_hide_on_categories       = get_post_meta( $post->ID, '_whp_hide_on_categories', true );
		$whp_hide_on_search           = get_post_meta( $post->ID, '_whp_hide_on_search', true );
		$whp_hide_on_tags             = get_post_meta( $post->ID, '_whp_hide_on_tags', true );
		$whp_hide_on_authors          = get_post_meta( $post->ID, '_whp_hide_on_authors', true );
		$whp_hide_in_rss_feed         = get_post_meta( $post->ID, '_whp_hide_in_rss_feed', true );
		$whp_hide_on_blog_page        = get_post_meta( $post->ID, '_whp_hide_on_blog_page', true );
		$whp_hide_on_date             = get_post_meta( $post->ID, '_whp_hide_on_date', true );
		$whp_hide_on_post_navigation  = get_post_meta( $post->ID, '_whp_hide_on_post_navigation', true );
		$whp_hide_on_recent_posts     = get_post_meta( $post->ID, '_whp_hide_on_recent_posts', true );
		$whp_hide_on_cpt_archive      = get_post_meta( $post->ID, '_whp_hide_on_cpt_archive', true );
		$whp_hide_on_archive          = get_post_meta( $post->ID, '_whp_hide_on_archive', true );
		$whp_hide_on_rest_api         = get_post_meta( $post->ID, '_whp_hide_on_rest_api', true );
		$whp_hide_on_single_post_page = get_post_meta( $post->ID, '_whp_hide_on_single_post_page', true );

		if ( whp_plugin()->is_woocommerce_active() && whp_plugin()->is_woocommerce_product() ) {
			$whp_hide_on_store            = get_post_meta( $post->ID, '_whp_hide_on_store', true );
			$whp_hide_on_product_category = get_post_meta( $post->ID, '_whp_hide_on_product_category', true );
		}

		$enabled_post_types = whp_plugin()->get_enabled_post_types();

		require_once WHP_PLUGIN_DIR . 'views/admin/template-admin-post-metabox.php';
	}

	/**
	 * Save post hide fields on post save/update
	 *
	 * @param  int      $post_id Curretn post id.
	 * @param  \WP_POST $post    Current post object.
	 *
	 * @return mixed          Returns post id or void
	 */
	public function save_post_metabox( $post_id, $post ) {
		// If revision, skip.
		if ( 'revision' === $post->post_type ) {
			return $post_id;
		}

		// Check if our nonce is set.
		if ( ! isset( $_POST['wp_metabox_nonce_value'] ) ) {
			return $post_id;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp_metabox_nonce_value'] ) ), 'wp_metabox_nonce' ) ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$enabled_post_types = whp_plugin()->get_enabled_post_types();

		if ( ! in_array( $post->post_type, $enabled_post_types, true ) ) {
			return $post_id;
		}

		$args = $_POST;

		// Data to be stored in the database.
		$data['_whp_hide_on_frontpage']        = ! empty( $args['whp_hide_on_frontpage'] ) ? true : false;
		$data['_whp_hide_on_categories']       = ! empty( $args['whp_hide_on_categories'] ) ? true : false;
		$data['_whp_hide_on_search']           = ! empty( $args['whp_hide_on_search'] ) ? true : false;
		$data['_whp_hide_on_tags']             = ! empty( $args['whp_hide_on_tags'] ) ? true : false;
		$data['_whp_hide_on_authors']          = ! empty( $args['whp_hide_on_authors'] ) ? true : false;
		$data['_whp_hide_in_rss_feed']         = ! empty( $args['whp_hide_in_rss_feed'] ) ? true : false;
		$data['_whp_hide_on_blog_page']        = ! empty( $args['whp_hide_on_blog_page'] ) ? true : false;
		$data['_whp_hide_on_date']             = ! empty( $args['whp_hide_on_date'] ) ? true : false;
		$data['_whp_hide_on_post_navigation']  = ! empty( $args['whp_hide_on_post_navigation'] ) ? true : false;
		$data['_whp_hide_on_recent_posts']     = ! empty( $args['whp_hide_on_recent_posts'] ) ? true : false;
		$data['_whp_hide_on_archive']          = ! empty( $args['whp_hide_on_archive'] ) ? true : false;
		$data['_whp_hide_on_cpt_archive']      = ! empty( $args['whp_hide_on_cpt_archive'] ) ? true : false;
		$data['_whp_hide_on_rest_api']         = ! empty( $args['whp_hide_on_rest_api'] ) ? true : false;
		$data['_whp_hide_on_single_post_page'] = ! empty( $args['whp_hide_on_single_post_page'] ) ? true : false;

		if ( whp_plugin()->is_woocommerce_active() && whp_plugin()->is_woocommerce_product() ) {
			$data['_whp_hide_on_store']            = ! empty( $args['whp_hide_on_store'] ) ? true : false;
			$data['_whp_hide_on_product_category'] = ! empty( $args['whp_hide_on_product_category'] ) ? true : false;
		}

		// Sanitize inputs.
		$this->sanitize_inputs( $data );

		// Save meta.
		$this->save_meta_data( $data, $post_id );

		$hide_types = array_keys( \MartinCV\WHP\Core\Constants::HIDDEN_POSTS_KEYS_LIST );

		foreach ( $hide_types as $hide_type ) {
			$key = 'whp_' . $post->post_type . '_' . $hide_type;

			wp_cache_delete( $key, 'whp' );
			delete_transient( $key );
		}
	}

	/**
	 * Save post meta data
	 *
	 * @param  array $meta_data The meta data array.
	 * @param  int   $post_id   Current post id.
	 *
	 * @return void
	 */
	private function save_meta_data( $meta_data, $post_id ) {
		foreach ( $meta_data as $key => $value ) {
			if ( get_post_meta( $post_id, $key, false ) ) {
				update_post_meta( $post_id, $key, $value );
			} else {
				add_post_meta( $post_id, $key, $value );
			}

			if ( ! $value ) {
				delete_post_meta( $post_id, $key );
			}
		}
	}

	/**
	 * Sanitize post inputs
	 *
	 * @param  array $post_data Post data array.
	 *
	 * @return void
	 */
	private function sanitize_inputs( &$post_data ) {
		$sanitized_data = array();

		foreach ( $post_data as $key => $value ) {
			if ( is_array( $value ) ) {
				$sanitized_data[ $key ] = array();

				foreach ( $value as $v ) {
					$sanitized_data[ $key ][] = sanitize_text_field( wp_unslash( $v ) );
				}
			} else {
				$sanitized_data[ $key ] = sanitize_meta( $key, $value, 'post' );
			}
		}

		$post_data = $sanitized_data;
	}
}
