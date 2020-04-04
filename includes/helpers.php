<?php
/**
 * Helper functions.
 *
 * @package  wordpress-hide-posts
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'whp_wc_exists' ) ) {
    function whp_wc_exists() {
        $plugin = 'woocommerce/woocommerce.php';
        return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
    }
}

if ( ! function_exists( 'whp_admin_wc_product' ) ) {
    function whp_admin_wc_product() {
        global $post;

        return $post->post_type === 'product';
    }
}
