<?php
/**
 * Manages WHP Post Meta Inputs.
 *
 * @package  wordpress-hide-posts
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WHP_Post_Hide_Metabox class.
 */
class WHP_Post_Hide_Metabox {
	/**
	 * Function: __construct
	 *
	 */
	public function __construct() {
		$whp_disable_hidden_on_column = get_option( 'whp_disable_hidden_on_column' );

		add_action( 'add_meta_boxes', array( $this, 'whp_add_metabox' ) );
		add_action( 'save_post', array( $this, 'whp_save_post_metabox' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'whp_load_admin_assets' ) );

		if ( ! $whp_disable_hidden_on_column ) {
			add_action( 'manage_posts_custom_column', array( $this, 'whp_render_post_columns' ), 10, 2 );

			add_filter( 'manage_posts_columns', array( $this, 'whp_add_post_columns' ) );
			add_filter( 'manage_pages_columns', array( $this, 'whp_add_post_columns' ) );
		}
	}

	/**
	 * Load admin assets
	 *
	 * @return  void  
	 */
	public function whp_load_admin_assets() {
		wp_register_script(
			'whp-admin-post-script',
			WHP_PLUGIN_URL . 'assets/admin/js/whp-script.js',
			array('jquery'),
			filemtime( WHP_PLUGIN_DIR . 'assets/admin/js/whp-script.js' ),
			true
		);

		wp_register_style(
			'whp-admin-post-style',
			WHP_PLUGIN_URL . 'assets/admin/css/whp-style.css',
			[],
			filemtime( WHP_PLUGIN_DIR . 'assets/admin/css/whp-style.css' )
		);
	}

	/**
	 * Add Post Hide metabox in sidebar top
	 *
	 * @return void
	 */
	public function whp_add_metabox() {
		$post_types = $this->_get_enabled_post_types();

		add_meta_box(
		  'whp_hide_posts',
		  __( 'Hide Posts', 'whp' ),
		  [ $this, 'whp_metabox_callback' ],
		  $post_types,
		  'side',
		  'high'
		);
	}

	/**
	 * Add custom columns in posts list table
	 *
	 * @param   array  $columns  
	 *
	 * @return  array            
	 */
	public function whp_add_post_columns( $columns ) {
		global $post;

		$enabled_post_types = $this->_get_enabled_post_types();

		if ( ! in_array( $post->post_type, $enabled_post_types ) ) {
			return $columns;
		}

		$columns['whp_hidden_on'] = __( 'Hidden On', 'whp' );
    	return $columns;
	}

	/**
	 * Show on which pages the post is hidden
	 *
	 * @param   string  $column_name  
	 * @param   string  $post_id      
	 *
	 * @return  void                
	 */
	public function whp_render_post_columns( $column_name, $post_id ) {
		if ( 'whp_hidden_on' !== $column_name ) {
			return;
		}

		$whp_hide_on_frontpage  = get_post_meta( $post_id, "_whp_hide_on_frontpage", true );
		$whp_hide_on_categories  = get_post_meta( $post_id, "_whp_hide_on_categories", true );
		$whp_hide_on_search  = get_post_meta( $post_id, "_whp_hide_on_search", true );
		$whp_hide_on_tags  = get_post_meta( $post_id, "_whp_hide_on_tags", true );
		$whp_hide_on_authors  = get_post_meta( $post_id, "_whp_hide_on_authors", true );
		$whp_hide_in_rss_feed  = get_post_meta( $post_id, "_whp_hide_in_rss_feed", true );
		$whp_hide_on_blog_page  = get_post_meta( $post_id, "_whp_hide_on_blog_page", true );
		$whp_hide_on_date  = get_post_meta( $post_id, "_whp_hide_on_date", true );
		$whp_hide_on_post_navigation  = get_post_meta( $post_id, "_whp_hide_on_post_navigation", true );

		if ( whp_wc_exists() && whp_admin_wc_product() ) {
			$whp_hide_on_store  = get_post_meta( $post_id, "_whp_hide_on_store", true );
			$whp_hide_on_product_category  = get_post_meta( $post_id, "_whp_hide_on_product_category", true );
		}

		$hide_on = '';

		if ( $whp_hide_on_frontpage ) {
			$hide_on .= __( 'Front / Home page' ) . ', ';
		}

		if ( $whp_hide_on_categories ) {
			$hide_on .= __( 'Categories archive' ) . ', ';
		}

		if ( $whp_hide_on_search ) {
			$hide_on .= __( 'Search page' ) . ', ';
		}

		if ( $whp_hide_on_tags ) {
			$hide_on .= __( 'Tags archive' ) . ', ';
		}

		if ( $whp_hide_on_authors ) {
			$hide_on .= __( 'Authors archive' ) . ', ';
		}

		if ( $whp_hide_in_rss_feed ) {
			$hide_on .= __( 'RSS feed' ) . ', ';
		}

		if ( $whp_hide_on_blog_page ) {
			$hide_on .= __( 'Blog page' ) . ', ';
		}

		if ( $whp_hide_on_date ) {
			$hide_on .= __( 'Date archive' ) . ', ';
		}

		if ( $whp_hide_on_post_navigation ) {
			$hide_on .= __( 'Single post navigation' ) . ', ';
		}

		if ( isset( $whp_hide_on_store ) && $whp_hide_on_store ) {
			$hide_on .= __( 'Store page' ) . ', ';
		}

		if ( isset( $whp_hide_on_product_category ) && $whp_hide_on_product_category ) {
			$hide_on .= __( 'Product category page' ) . ', ';
		}

		if ( '' !== $hide_on ) {
			$hide_on = rtrim( $hide_on, ', ' );
			echo $hide_on;
		}
	}

	/**
	 * Show the metabox template in sidebar top
	 *
	 * @param  WP_Post $post Current post object.
	 *
	 * @return void
	 */
	public function whp_metabox_callback( $post ) {
		wp_nonce_field( 'wp_whp_metabox_nonce',  'wp_whp_metabox_nonce_value' );

		$whp_hide_on_frontpage  = get_post_meta( $post->ID, "_whp_hide_on_frontpage", true );
		$whp_hide_on_categories  = get_post_meta( $post->ID, "_whp_hide_on_categories", true );
		$whp_hide_on_search  = get_post_meta( $post->ID, "_whp_hide_on_search", true );
		$whp_hide_on_tags  = get_post_meta( $post->ID, "_whp_hide_on_tags", true );
		$whp_hide_on_authors  = get_post_meta( $post->ID, "_whp_hide_on_authors", true );
		$whp_hide_in_rss_feed  = get_post_meta( $post->ID, "_whp_hide_in_rss_feed", true );
		$whp_hide_on_blog_page  = get_post_meta( $post->ID, "_whp_hide_on_blog_page", true );
		$whp_hide_on_date  = get_post_meta( $post->ID, "_whp_hide_on_date", true );
		$whp_hide_on_post_navigation  = get_post_meta( $post->ID, "_whp_hide_on_post_navigation", true );

		if ( whp_wc_exists() && whp_admin_wc_product() ) {
			$whp_hide_on_store  = get_post_meta( $post->ID, "_whp_hide_on_store", true );
			$whp_hide_on_product_category  = get_post_meta( $post->ID, "_whp_hide_on_product_category", true );
		}

		$enabled_post_types = get_option( 'whp_enabled_post_types' );

		wp_enqueue_script( 'whp-admin-post-script' );

		@include_once ( WHP_PLUGIN_DIR . 'views/admin/template-admin-post-metabox.php' );
	}

	/**
	 * Save post hide fields on post save/update
	 *
	 * @param  int $post_id Curretn post id.
	 * @param  WP_POST $post    Current post object.
	 *
	 * @return Mixed          Returns post id or void
	 */
	public function whp_save_post_metabox( $post_id, $post ) {
		// If revision, skip
		if( $post->post_type === 'revision' ) {
			return $post_id;
		}

		// Check if our nonce is set.
		if ( ! isset( $_POST['wp_whp_metabox_nonce_value'] ) ) {
		    return $post_id;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['wp_whp_metabox_nonce_value'], 'wp_whp_metabox_nonce' ) ) {
		    return $post_id;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
		    return $post_id;
		}

		// Data to be stored in the database.
		$whp_data['_whp_hide_on_frontpage']	= ! empty( $_POST['whp_hide_on_frontpage'] ) ? true : false;
		$whp_data['_whp_hide_on_categories'] = ! empty( $_POST['whp_hide_on_categories'] ) ? true : false;
		$whp_data['_whp_hide_on_search'] = ! empty($_POST['whp_hide_on_search'] ) ? true : false;
		$whp_data['_whp_hide_on_tags'] = ! empty( $_POST['whp_hide_on_tags'] ) ? true : false;
		$whp_data['_whp_hide_on_authors'] = ! empty( $_POST['whp_hide_on_authors'] ) ? true : false;
		$whp_data['_whp_hide_in_rss_feed'] = ! empty( $_POST['whp_hide_in_rss_feed'] ) ? true : false;
		$whp_data['_whp_hide_on_blog_page'] = ! empty( $_POST['whp_hide_on_blog_page'] ) ? true : false;
		$whp_data['_whp_hide_on_date'] = ! empty( $_POST['whp_hide_on_date'] ) ? true : false;
		$whp_data['_whp_hide_on_post_navigation'] = ! empty( $_POST['whp_hide_on_post_navigation'] ) ? true : false;

		if ( whp_wc_exists() && whp_admin_wc_product() ) {
			$whp_data['_whp_hide_on_store'] = ! empty( $_POST['whp_hide_on_store'] ) ? true : false;
			$whp_data['_whp_hide_on_product_category'] = ! empty( $_POST['whp_hide_on_product_category'] ) ? true : false;
		}

		// Sanitize inputs.
		$this->_sanitize_inputs( $whp_data );

		// Save meta.
		$this->_save_meta_data( $whp_data, $post_id );
	}

  /**
   * Save post meta data
   *
   * @param  array $meta_data The meta data array.
   * @param  int $post_id   Current post id.
   *
   * @return void
   */
	private function _save_meta_data( $meta_data, $post_id ) {
		foreach ( $meta_data as $key => $value ) {
			if ( get_post_meta( $post_id, $key, FALSE ) ) {
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
	* @return void
	*/
	private function _sanitize_inputs( &$post_data ) {
		$sanitized_data = array();
		
		foreach ( $post_data as $key => $value ) {
			$sanitized_data[ $key ] = sanitize_meta( $key, $value, 'post' );
		}

		$post_data = $sanitized_data;
	}

	/**
	 * Get post types that have the WHP funcionality enabled
	 *
	 * @return  array  
	 */
	private function _get_enabled_post_types() {
		$key = 'whp_pt';

		$post_types = wp_cache_get( $key );

		if ( $post_types ) {
			return $post_types;
		}

		$post_types = [ 'post' ];
		$enabled_post_types = get_option( 'whp_enabled_post_types' );

		if ( is_array( $enabled_post_types ) ) {
			$post_types = array_merge( $post_types, $enabled_post_types );
		}

		wp_cache_set( $key, $post_types, 'whp_data' );

		return $post_types;
	}
}
