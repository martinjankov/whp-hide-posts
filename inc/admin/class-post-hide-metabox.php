<?php
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
		$disable_hidden_on_column = get_option( 'disable_hidden_on_column' );

		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'save_post_metabox' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_assets' ) );

		if ( ! $disable_hidden_on_column ) {
			add_action( 'manage_posts_custom_column', array( $this, 'render_post_columns' ), 10, 2 );

			add_filter( 'manage_posts_columns', array( $this, 'add_post_columns' ) );
			add_filter( 'manage_pages_columns', array( $this, 'add_post_columns' ) );
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

        $enabled_post_types = whp_get_enabled_post_types();

        if ( ! in_array( $post->post_type, $enabled_post_types ) ) {
            return;
        }

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
	public function add_metabox() {
		$post_types = whp_get_enabled_post_types();
        // var_dump($post_types); die;
		add_meta_box(
		  'hide_posts',
		  __( 'Hide Posts', 'whp' ),
		  [ $this, 'metabox_callback' ],
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
	public function add_post_columns( $columns ) {
		global $post;

		$enabled_post_types = whp_get_enabled_post_types();

		if ( ! in_array( $post->post_type, $enabled_post_types ) ) {
			return $columns;
		}

		$columns['hidden_on'] = __( 'Hidden On', 'whp' );
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
	public function render_post_columns( $column_name, $post_id ) {
		if ( 'hidden_on' !== $column_name ) {
			return;
		}

		$whp_hide_on_frontpage  = get_post_meta( $post_id, "_whp_hide_on_frontpage", true );
		$whp_hide_on_categories  = get_post_meta( $post_id, "_whp_hide_on_categories", true );
		$whp_hide_on_search  = get_post_meta( $post_id, "_whp_hide_on_search", true );
		$whp_hide_on_tags  = get_post_meta( $post_id, "_whp_hide_on_tags", true );
		$whp_hide_on_authors  = get_post_meta( $post_id, "_whp_hide_on_authors", true );
		$whp_hide_in_rss_feed  = get_post_meta( $post_id, "_hide_in_rss_feed", true );
		$whp_hide_on_blog_page  = get_post_meta( $post_id, "_whp_hide_on_blog_page", true );
		$whp_hide_on_date  = get_post_meta( $post_id, "_whp_hide_on_date", true );
		$whp_hide_on_post_navigation  = get_post_meta( $post_id, "_whp_hide_on_post_navigation", true );
		$whp_hide_on_recent_posts  = get_post_meta( $post_id, "_whp_hide_on_recent_posts", true );
		$whp_hide_on_cpt_archive  = get_post_meta( $post_id, "_whp_hide_on_cpt_archive", true );
		$whp_hide_on_cpt_tax  = get_post_meta( $post_id, "_whp_hide_on_cpt_tax", true );

		if ( whp_wc_exists() && whp_admin_wc_product() ) {
			$whp_hide_on_store  = get_post_meta( $post_id, "_whp_hide_on_store", true );
			$whp_hide_on_product_category  = get_post_meta( $post_id, "_whp_hide_on_product_category", true );
		}

		$whp_hide_on = '';

		if ( $whp_hide_on_frontpage ) {
			$whp_hide_on .= __( 'Front / Home page' ) . ', ';
		}

		if ( $whp_hide_on_categories ) {
			$whp_hide_on .= __( 'Categories archive' ) . ', ';
		}

		if ( $whp_hide_on_search ) {
			$whp_hide_on .= __( 'Search page' ) . ', ';
		}

		if ( $whp_hide_on_tags ) {
			$whp_hide_on .= __( 'Tags archive' ) . ', ';
		}

		if ( $whp_hide_on_authors ) {
			$whp_hide_on .= __( 'Authors archive' ) . ', ';
		}

		if ( $whp_hide_in_rss_feed ) {
			$whp_hide_on .= __( 'RSS feed' ) . ', ';
		}

		if ( $whp_hide_on_blog_page ) {
			$whp_hide_on .= __( 'Blog page' ) . ', ';
		}

		if ( $whp_hide_on_date ) {
			$whp_hide_on .= __( 'Date archive' ) . ', ';
		}

		if ( $whp_hide_on_post_navigation ) {
			$whp_hide_on .= __( 'Single post navigation' ) . ', ';
		}

		if ( $whp_hide_on_recent_posts ) {
			$whp_hide_on .= __( 'Recent Posts Widget' ) . ', ';
		}

		if ( $whp_hide_on_cpt_archive ) {
			$whp_hide_on .= __( 'CPT Archive page' ) . ', ';
		}

		if ( $whp_hide_on_cpt_tax ) {
			$whp_hide_on .= __( 'CPT tax page' ) . ', ';
		}

		if ( isset( $whp_hide_on_store ) && $whp_hide_on_store ) {
			$whp_hide_on .= __( 'Store page' ) . ', ';
		}

		if ( isset( $whp_hide_on_product_category ) && $whp_hide_on_product_category ) {
			$whp_hide_on .= __( 'Product category page' ) . ', ';
		}

		if ( '' !== $whp_hide_on ) {
			$whp_hide_on = rtrim( $whp_hide_on, ', ' );
			echo $whp_hide_on;
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
		wp_nonce_field( 'wp_metabox_nonce',  'wp_metabox_nonce_value' );

		$whp_hide_on_frontpage  = get_post_meta( $post->ID, "_whp_hide_on_frontpage", true );
		$whp_hide_on_categories  = get_post_meta( $post->ID, "_whp_hide_on_categories", true );
		$whp_hide_on_search  = get_post_meta( $post->ID, "_whp_hide_on_search", true );
		$whp_hide_on_tags  = get_post_meta( $post->ID, "_whp_hide_on_tags", true );
		$whp_hide_on_authors  = get_post_meta( $post->ID, "_whp_hide_on_authors", true );
		$whp_hide_in_rss_feed  = get_post_meta( $post->ID, "_hide_in_rss_feed", true );
		$whp_hide_on_blog_page  = get_post_meta( $post->ID, "_whp_hide_on_blog_page", true );
		$whp_hide_on_date  = get_post_meta( $post->ID, "_whp_hide_on_date", true );
		$whp_hide_on_post_navigation  = get_post_meta( $post->ID, "_whp_hide_on_post_navigation", true );
		$whp_hide_on_recent_posts  = get_post_meta( $post->ID, "_whp_hide_on_recent_posts", true );
		$whp_hide_on_cpt_archive  = get_post_meta( $post->ID, "_whp_hide_on_cpt_archive", true );
		$whp_hide_on_cpt_tax  = get_post_meta( $post->ID, "_whp_hide_on_cpt_tax", true );

		if ( whp_wc_exists() && whp_admin_wc_product() ) {
			$whp_hide_on_store  = get_post_meta( $post->ID, "_whp_hide_on_store", true );
			$whp_hide_on_product_category  = get_post_meta( $post->ID, "_whp_hide_on_product_category", true );
		}

		$enabled_post_types = whp_get_enabled_post_types();

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
	public function save_post_metabox( $post_id, $post ) {
		// If revision, skip
		if( $post->post_type === 'revision' ) {
			return $post_id;
		}

		// Check if our nonce is set.
		if ( ! isset( $_POST['wp_metabox_nonce_value'] ) ) {
		    return $post_id;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['wp_metabox_nonce_value'], 'wp_metabox_nonce' ) ) {
		    return $post_id;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
		    return $post_id;
		}
		// Data to be stored in the database.
		$data['_whp_hide_on_frontpage']	= ! empty( $_POST['whp_hide_on_frontpage'] ) ? true : false;
		$data['_whp_hide_on_categories'] = ! empty( $_POST['whp_hide_on_categories'] ) ? true : false;
		$data['_whp_hide_on_search'] = ! empty($_POST['whp_hide_on_search'] ) ? true : false;
		$data['_whp_hide_on_tags'] = ! empty( $_POST['whp_hide_on_tags'] ) ? true : false;
		$data['_whp_hide_on_authors'] = ! empty( $_POST['whp_hide_on_authors'] ) ? true : false;
		$data['_hide_in_rss_feed'] = ! empty( $_POST['hide_in_rss_feed'] ) ? true : false;
		$data['_whp_hide_on_blog_page'] = ! empty( $_POST['whp_hide_on_blog_page'] ) ? true : false;
		$data['_whp_hide_on_date'] = ! empty( $_POST['whp_hide_on_date'] ) ? true : false;
		$data['_whp_hide_on_post_navigation'] = ! empty( $_POST['whp_hide_on_post_navigation'] ) ? true : false;
		$data['_whp_hide_on_recent_posts'] = ! empty( $_POST['whp_hide_on_recent_posts'] ) ? true : false;
		$data['_whp_hide_on_cpt_archive'] = ! empty( $_POST['whp_hide_on_cpt_archive'] ) ? true : false;
		$data['_whp_hide_on_cpt_tax'] = ! empty( $_POST['whp_hide_on_cpt_tax'] ) ? $_POST['whp_hide_on_cpt_tax'] : false;

		if ( whp_wc_exists() && whp_admin_wc_product() ) {
			$data['_whp_hide_on_store'] = ! empty( $_POST['whp_hide_on_store'] ) ? true : false;
			$data['_whp_hide_on_product_category'] = ! empty( $_POST['whp_hide_on_product_category'] ) ? true : false;
		}

		// Sanitize inputs.
		$this->_sanitize_inputs( $data );

		// Save meta.
		$this->_save_meta_data( $data, $post_id );
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
            if ( is_array( $value ) ) {
                $sanitized_data[ $key ] = array();

                foreach ( $value as $v ) {
                    $sanitized_data[ $key ][] = sanitize_text_field( $v );
                }
            } else {
                $sanitized_data[ $key ] = sanitize_meta( $key, $value, 'post' );
            }
		}

		$post_data = $sanitized_data;
	}
}
